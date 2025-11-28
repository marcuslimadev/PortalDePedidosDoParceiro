import nodemailer from 'nodemailer';
import dotenv from 'dotenv';

dotenv.config();

// Configuração do transporte de email
const createTransporter = () => {
  // Em produção, usar serviço real (SendGrid, AWS SES, etc)
  // Em desenvolvimento, usar Ethereal (email de teste)
  if (process.env.NODE_ENV === 'production' && process.env.SMTP_HOST) {
    return nodemailer.createTransport({
      host: process.env.SMTP_HOST,
      port: parseInt(process.env.SMTP_PORT || '587'),
      secure: process.env.SMTP_SECURE === 'true',
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASSWORD
      }
    });
  }

  // Modo desenvolvimento: log emails no console
  return nodemailer.createTransport({
    streamTransport: true,
    newline: 'unix',
    buffer: true
  });
};

const transporter = createTransporter();

/**
 * Envia email para um ou mais destinatários
 * @param {string|string[]} to - Email(s) do(s) destinatário(s)
 * @param {string} subject - Assunto do email
 * @param {string} html - Conteúdo HTML do email
 * @param {string} text - Conteúdo texto plano (fallback)
 */
export const sendEmail = async (to, subject, html, text = null) => {
  try {
    const mailOptions = {
      from: process.env.SMTP_FROM || 'Portal de Pedidos <noreply@portalpedidos.com>',
      to: Array.isArray(to) ? to.join(', ') : to,
      subject,
      html,
      text: text || html.replace(/<[^>]*>/g, '') // Remove HTML tags para texto plano
    };

    const info = await transporter.sendMail(mailOptions);

    if (process.env.NODE_ENV !== 'production') {
      console.log('📧 Email enviado (modo dev):');
      console.log('   Para:', mailOptions.to);
      console.log('   Assunto:', mailOptions.subject);
      console.log('   Preview:', info.message?.toString().substring(0, 200));
    }

    return { success: true, messageId: info.messageId };
  } catch (error) {
    console.error('Erro ao enviar email:', error);
    return { success: false, error: error.message };
  }
};

/**
 * Template para email de novo pedido
 */
export const emailTemplates = {
  novoPedido: (order, lojaNome) => ({
    subject: `Novo pedido #${order.id} - ${lojaNome}`,
    html: `
      <h2>Novo Pedido Registrado</h2>
      <p><strong>Pedido:</strong> #${order.id}</p>
      <p><strong>Loja:</strong> ${lojaNome}</p>
      <p><strong>Valor Total:</strong> R$ ${order.total?.toFixed(2)}</p>
      <p><strong>Status:</strong> ${order.status}</p>
      <p><strong>Data:</strong> ${new Date(order.created_at).toLocaleString('pt-BR')}</p>
      <hr>
      <p>Acesse o portal para mais detalhes.</p>
    `
  }),

  statusAtualizado: (order, lojaNome, novoStatus) => ({
    subject: `Pedido #${order.id} - Status atualizado para ${novoStatus}`,
    html: `
      <h2>Status do Pedido Atualizado</h2>
      <p><strong>Pedido:</strong> #${order.id}</p>
      <p><strong>Loja:</strong> ${lojaNome}</p>
      <p><strong>Novo Status:</strong> ${novoStatus}</p>
      <p><strong>Valor Total:</strong> R$ ${order.total?.toFixed(2)}</p>
      <hr>
      <p>Acesse o portal para acompanhar seu pedido.</p>
    `
  }),

  pedidoAprovado: (order, lojaNome) => ({
    subject: `Pedido #${order.id} aprovado!`,
    html: `
      <h2>🎉 Pedido Aprovado</h2>
      <p><strong>Pedido:</strong> #${order.id}</p>
      <p><strong>Loja:</strong> ${lojaNome}</p>
      <p><strong>Valor Total:</strong> R$ ${order.total?.toFixed(2)}</p>
      <p>Seu pedido foi aprovado e será processado em breve.</p>
      <hr>
      <p>Acesse o portal para mais detalhes.</p>
    `
  }),

  pedidoCancelado: (order, lojaNome, motivo = null) => ({
    subject: `Pedido #${order.id} cancelado`,
    html: `
      <h2>Pedido Cancelado</h2>
      <p><strong>Pedido:</strong> #${order.id}</p>
      <p><strong>Loja:</strong> ${lojaNome}</p>
      ${motivo ? `<p><strong>Motivo:</strong> ${motivo}</p>` : ''}
      <hr>
      <p>Se tiver dúvidas, entre em contato com nosso suporte.</p>
    `
  })
};
