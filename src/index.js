import React from "react";
import { render } from "react-dom";
import App from "./components/App";
import { Provider } from "react-redux";
import store from "./store/index";

import './index.css';

global.baseUrl = process.env.NODE_ENV === 'development' ?
  '' : '../index.php';

render(
  <Provider store={store}>
    <App />
  </Provider>,
  document.getElementById('root')
);
