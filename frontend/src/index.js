import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import App from "./App";
import config from "./config";

const root = ReactDOM.createRoot(document.getElementById("root"));

root.render(
  <BrowserRouter basename={config.BASENAME}>
    <App />
  </BrowserRouter>
);
