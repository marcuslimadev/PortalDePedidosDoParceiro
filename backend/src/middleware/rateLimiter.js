import rateLimit from 'express-rate-limit';

/**
 * Rate limiter geral para todas as rotas da API
 * 100 requisições por 15 minutos por IP
 */
export const generalLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutos
  max: 100, // Limite de 100 requisições por janela
  message: {
    error: 'Muitas requisições deste IP, tente novamente em 15 minutos'
  },
  standardHeaders: true, // Retorna informações de rate limit nos headers
  legacyHeaders: false
});

/**
 * Rate limiter estrito para login
 * 5 tentativas por 15 minutos por IP
 */
export const loginLimiter = rateLimit({
  windowMs: 15 * 60 * 1000, // 15 minutos
  max: 5, // Limite de 5 tentativas de login por janela
  message: {
    error: 'Muitas tentativas de login. Tente novamente em 15 minutos'
  },
  skipSuccessfulRequests: true, // Não conta requisições bem-sucedidas
  standardHeaders: true,
  legacyHeaders: false
});

/**
 * Rate limiter para criação de pedidos
 * 20 pedidos por hora por IP
 */
export const orderCreationLimiter = rateLimit({
  windowMs: 60 * 60 * 1000, // 1 hora
  max: 20, // Limite de 20 pedidos por hora
  message: {
    error: 'Limite de criação de pedidos atingido. Tente novamente em 1 hora'
  },
  standardHeaders: true,
  legacyHeaders: false
});

/**
 * Rate limiter para registro de usuários
 * 3 registros por hora por IP
 */
export const registrationLimiter = rateLimit({
  windowMs: 60 * 60 * 1000, // 1 hora
  max: 3, // Limite de 3 registros por hora
  message: {
    error: 'Muitos registros deste IP. Tente novamente em 1 hora'
  },
  standardHeaders: true,
  legacyHeaders: false
});

/**
 * Rate limiter para exportação de dados
 * 10 exportações por hora por IP
 */
export const exportLimiter = rateLimit({
  windowMs: 60 * 60 * 1000, // 1 hora
  max: 10, // Limite de 10 exportações por hora
  message: {
    error: 'Limite de exportações atingido. Tente novamente em 1 hora'
  },
  standardHeaders: true,
  legacyHeaders: false
});

/**
 * Rate limiter para operações de admin
 * 100 requisições por 15 minutos por IP
 */
export const adminLimiter = rateLimit({
  windowMs: 15 * 60 * 1000,
  max: 100,
  message: {
    error: 'Limite de requisições atingido'
  },
  standardHeaders: true,
  legacyHeaders: false
});
