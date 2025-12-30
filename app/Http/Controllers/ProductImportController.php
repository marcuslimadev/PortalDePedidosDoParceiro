<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\SimpleExcel\SimpleExcelReader;

class ProductImportController extends Controller
{
    public function create(): View
    {
        return view('products.import');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,txt',
        ]);

        // Armazena arquivo e obtém caminho completo
        $path = $data['file']->store('imports');
        $fullPath = Storage::disk('local')->path($path);

        try {
            $rows = SimpleExcelReader::create($fullPath)->getRows();
            $count = 0;

            foreach ($rows as $row) {
                $normalized = $this->normalizeRow($row);
                if (empty($normalized['codigo']) || empty($normalized['descricao'])) {
                    continue;
                }

                Product::updateOrCreate(
                    ['codigo' => $normalized['codigo']],
                    $normalized
                );
                $count++;
            }

            // Deleta arquivo após importação
            Storage::disk('local')->delete($path);

            return back()->with('success', "Importação concluída: {$count} produtos processados.");
        } catch (\Exception $e) {
            return back()->withErrors("Erro ao processar arquivo: " . $e->getMessage());
        }
    }

    private function normalizeRow(array $row): array
    {
        // Normaliza chaves do header (case-insensitive, remove acentos e espaços)
        $normalized = [];
        foreach ($row as $key => $value) {
            $slug = (string) Str::of($key)
                ->lower()
                ->replace(['.', '-', ' '], '_')
                ->ascii();
            $normalized[$slug] = $value;
        }

        $get = fn(string $key) => $normalized[$key] ?? null;

        $decimal = function ($val, $default = 0.0) {
            if ($val === null || $val === '') {
                return $default;
            }
            // Remove espaços, símbolos de moeda
            $v = trim(str_replace([' ', 'R$', 'r$', 'R$ ', '$'], '', (string) $val));
            
            // Se tem vírgula e ponto, assume formato brasileiro (1.234,56)
            if (strpos($v, ',') !== false && strpos($v, '.') !== false) {
                $v = str_replace('.', '', $v); // remove separador de milhar
                $v = str_replace(',', '.', $v); // troca vírgula decimal por ponto
            }
            // Se tem apenas vírgula, assume como separador decimal
            elseif (strpos($v, ',') !== false) {
                $v = str_replace(',', '.', $v);
            }
            
            $v = preg_replace('/[^0-9.-]/', '', $v); // remove caracteres não numéricos
            return is_numeric($v) ? (float) $v : $default;
        };

        $int = function ($val) {
            if ($val === null || $val === '') {
                return 0;
            }
            return (int) preg_replace('/[^0-9-]/', '', (string) $val);
        };

        return [
            'codigo'        => $get('codigo') ?? $get('codprod') ?? $get('cod_prod') ?? $get('codigo_produto'),
            'descricao'     => $get('descricao') ?? $get('descrição') ?? $get('produto') ?? $get('descricao_produto'),
            'preco'         => $decimal($get('preco') ?? $get('preço') ?? $get('valor') ?? $get('preco_venda') ?? $get('pvenda')),
            'unidade'       => $get('unidade') ?? $get('un') ?? $get('unidadedemedida') ?? 'UN',
            'tributacao'    => $get('tributacao') ?? $get('tributação') ?? $get('nbm') ?? 'T',
            'estoque'       => $int($get('estoque') ?? $get('qtd') ?? $get('quantidade') ?? $get('qtunit')),
            'categoria'     => $get('categoria') ?? $get('j13_categoria') ?? $get('j11_categoria') ?? $get('grupo') ?? $get('j8_descricao'),
            'codprod_winthor' => $get('codprod') ?? $get('codprod_winthor') ?? $get('winthor') ?? $get('cod_winthor'),
            'embalagem'     => $get('embalagem') ?? $get('embalagemmaster') ?? $get('pack') ?? $get('emb'),
            'marca'         => $get('marca') ?? $get('j9_marca') ?? $get('fabricante'),
            'peso_liquido'  => $decimal($get('peso_liquido') ?? $get('pesoliq') ?? $get('peso_liq')),
            'peso_bruto'    => $decimal($get('peso_bruto') ?? $get('pesobruto') ?? $get('peso_brt')),
            'ncm'           => $get('ncm') ?? $get('codigo_ncm'),
        ];
    }
}
