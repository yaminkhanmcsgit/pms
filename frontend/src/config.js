const env = window.__ENV__ || {};

const config = {
  API_URL: env.API_URL || "http://localhost/pms/",
  APP_ENV: env.APP_ENV || "development",
  DEBUG: env.DEBUG ?? false,
  BASENAME: process.env.NODE_ENV === 'production' ? (env.BASENAME || "/") : "/"
};

export default config;
