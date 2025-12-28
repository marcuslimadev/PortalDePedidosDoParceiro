<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $path = $data['file']->store('imports');
        $fullPath = storage_path('app/' . $path);

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

        return back()->with('success', "Importação concluída: {$count} produtos processados.");
    }

    private function normalizeRow(array $row): array
    {
        // Normaliza chaves do header (case-insensitive, remove acentos e espaços)
        $normalized = [];
        foreach ($row as $key => $value) {
            $slug = Str::of($key)
                ->lower()
                ->replace(['.', '-', ' '], '_')
                ->ascii();
            $normalized[$slug] = $value;
        }

        $get = fn(string $key) => $normalized[$key] ?? null;

        $decimal = function ($val) {
            if ($val === null || $val === '') {
                return null;
            }
            // troca vírgula por ponto e remove espaços
            $v = str_replace([' ', 'R$', 'r$', 'R$ '], '', $val);
            $v = str_replace(['.', ','], ['', '.'], $v); // remove milhar, troca decimal
            return is_numeric($v) ? (float) $v : null;
        };

        $int = function ($val) {
            if ($val === null || $val === '') {
                return 0;
            }
            return (int) preg_replace('/[^0-9-]/', '', (string) $val);
        };

        return [
            'codigo'        => $get('codigo') ?? $get('cod_prod') ?? $get('codprod') ?? $get('codigo_produto'),
            'descricao'     => $get('descricao') ?? $get('descrição') ?? $get('produto'),
            'preco'         => $decimal($get('preco') ?? $get('preço') ?? $get('valor') ?? $get('preco_venda')),
            'unidade'       => $get('unidade') ?? $get('un') ?? $get('unidadedemedida'),
            'tributacao'    => $get('tributacao') ?? $get('tributação'),
            'estoque'       => $int($get('estoque') ?? $get('qtd') ?? $get('quantidade')),
            'categoria'     => $get('categoria') ?? $get('grupo') ?? $get('linha'),
            'codprod_winthor' => $get('codprod_winthor') ?? $get('winthor'),
            'embalagem'     => $get('embalagem') ?? $get('pack'),
            'marca'         => $get('marca'),
            'peso_liquido'  => $decimal($get('peso_liquido') ?? $get('peso_liq')),
            'peso_bruto'    => $decimal($get('peso_bruto') ?? $get('peso_brt')),
            'ncm'           => $get('ncm'),
        ];
    }
}
