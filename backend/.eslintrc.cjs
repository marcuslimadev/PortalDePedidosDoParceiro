module.exports = {
  env: {
    node: true,
    es2022: true,
    jest: true
  },
  extends: ['standard'],
  parserOptions: {
    ecmaVersion: 'latest'
  },
  rules: {
    semi: ['error', 'always']
  }
};
