/**
 * Serviço para cálculo de condições de pagamento e descontos
 */

/**
 * Descontos disponíveis por condição de pagamento
 */
const paymentTermsDiscounts = {
  Antecipado: 0.05, // 5% de desconto
  '30 dias': 0.02, // 2% de desconto
  '45 dias': 0,
  '60 dias': 0,
  '90 dias': 0,
  '30/60': 0,
  '30/60/90': 0
};

/**
 * Calcula desconto baseado na condição de pagamento
 * @param {number} subtotal - Valor subtotal do pedido
 * @param {string} paymentTerms - Condição de pagamento
 * @returns {Object} { discount, finalTotal, discountPercentage }
 */
export const calculatePaymentDiscount = (subtotal, paymentTerms) => {
  const discountPercentage = paymentTermsDiscounts[paymentTerms] || 0;
  const discount = subtotal * discountPercentage;
  const finalTotal = subtotal - discount;

  return {
    discount: Number(discount.toFixed(2)),
    finalTotal: Number(finalTotal.toFixed(2)),
    discountPercentage: Number((discountPercentage * 100).toFixed(2))
  };
};

/**
 * Obtém a melhor condição de pagamento para o cliente
 * Prioriza: 1) Termo do pedido, 2) Termo do cliente, 3) Padrão '30 dias'
 * @param {string|null} requestedTerms - Termos solicitados no pedido
 * @param {string|null} clientTerms - Termos padrão do cliente
 * @returns {string} Condição de pagamento a ser aplicada
 */
export const getPaymentTerms = (requestedTerms, clientTerms) => {
  if (requestedTerms) return requestedTerms;
  if (clientTerms) return clientTerms;
  return '30 dias';
};

/**
 * Valida se a condição de pagamento é permitida
 */
export const allowedPaymentTerms = [
  '30 dias',
  '45 dias',
  '60 dias',
  '90 dias',
  '30/60',
  '30/60/90',
  'Antecipado'
];

/**
 * Calcula valores do pedido incluindo descontos
 * @param {number} subtotal - Valor subtotal dos itens
 * @param {string} paymentTerms - Condição de pagamento
 * @returns {Object} { subtotal, discount, total, paymentTerms }
 */
export const calculateOrderTotals = (subtotal, paymentTerms) => {
  const { discount, finalTotal, discountPercentage } = calculatePaymentDiscount(subtotal, paymentTerms);

  return {
    subtotal: Number(subtotal.toFixed(2)),
    discount,
    discountPercentage,
    total: finalTotal,
    paymentTerms
  };
};
