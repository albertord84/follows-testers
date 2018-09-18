import { assign } from "lodash-es";

const LOGIN = 'LOGIN';
const LOGGED = 'LOGGED';
const LOGIN_FAILED = 'LOGIN_FAILED';
const SET_LOGIN_USERNAME = 'SET_LOGIN_USERNAME';
const SET_LOGIN_PASSWORD = 'SET_LOGIN_PASSWORD';

const userState = {
  userName: process.env.NODE_ENV === 'development' ? 'yordanoweb' : '',
  password: process.env.NODE_ENV === 'development' ? 'Kaperuza25' : '',
  token: '',
  logged: process.env.NODE_ENV === 'development',
  logging: false,
  loginError: ''
};

export const user = (state = userState, action) => {
  switch (action.type) {
    case LOGIN:
      return assign({}, state, action.payload, {
        logging: true,
        loginError: ''
      });
  
    case LOGGED:
      return assign({}, state, action.payload, {
        logged: true,
        logging: false,
      });
  
    case LOGIN_FAILED:
      return assign({}, state, action.payload, {
        logged: false,
        logging: false,
      });

    case SET_LOGIN_USERNAME:
      return assign({}, state, {
        userName: action.payload,
        loginError: ''
      });
  
    case SET_LOGIN_PASSWORD:
      return assign({}, state, {
        password: action.payload,
        loginError: ''
      });
  
    default:
      return state;
  }
}

export const loginAction = (payload) => {
  return { type: LOGIN, payload: payload }
}

export const loggedAction = (payload) => {
  return { type: LOGGED, payload: payload }
}

export const loginFailedAction = (payload) => {
  return { type: LOGIN_FAILED, payload: payload }
}

export const setLoginUserNameAction = (userName) => {
  return { type: SET_LOGIN_USERNAME, payload: userName }
}

export const setLoginPasswordAction = (password) => {
  return { type: SET_LOGIN_PASSWORD, payload: password }
}

export default user;
