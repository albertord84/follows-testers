import { assign } from "lodash-es";

const SEND_LOGIN_TEST_DATA = 'SEND_LOGIN_TEST_DATA';
const SAVED_LOGGING_TEST = 'SAVED_LOGGING_TEST';
const SAVE_LOGGING_TEST_FAILED = 'SAVE_LOGGING_TEST_FAILED';
const LOADED_LOGGING_TEST_DATA = 'LOADED_LOGGING_TEST_DATA';
const LOGGING_TEST_LOG = 'LOGGING_TEST_LOG';
const SET_LOGIN_TEST_ACCOUNT_NAME = 'SET_LOGIN_TEST_ACCOUNT_NAME';
const SET_LOGIN_TEST_ACCOUNT_PASSWD = 'SET_LOGIN_TEST_ACCOUNT_PASSWD';
const SET_LOGIN_TEST_INTERVAL = 'SET_LOGIN_TEST_INTERVAL';
const SET_LOGIN_TEST_ACTIVE = 'SET_LOGIN_TEST_ACTIVE';

const loginTestState = {
  waiting: true,
  userName: '',
  password: '',
  interval: '',
  activated: '',
  error: '',
  times: 0,
  logLines: []
}

export const loginTest = (state = loginTestState, action) => {
  switch (action.type) {
    case SEND_LOGIN_TEST_DATA:
      return assign({}, state, {
        waiting: true,
        error: ''
      });
  
    case SAVED_LOGGING_TEST:
      return assign({}, state, action.payload, {
        waiting: false
      });

    case SAVE_LOGGING_TEST_FAILED:
      return assign({}, state, action.payload, {
        waiting: false
      });

    case LOADED_LOGGING_TEST_DATA:
      return assign({}, state, action.payload, {
        waiting: false
      });
    
    case LOGGING_TEST_LOG:
      return assign({}, state, {
        logLines: action.payload
      });
    
    case SET_LOGIN_TEST_ACCOUNT_NAME:
      return assign({}, state, { userName: action.payload });

    case SET_LOGIN_TEST_ACCOUNT_PASSWD:
      return assign({}, state, { password: action.payload });

    case SET_LOGIN_TEST_INTERVAL:
      return assign({}, state, { interval: action.payload });

    case SET_LOGIN_TEST_ACTIVE:
      return assign({}, state, { activated: action.payload });

    default:
      return state;
  }
}

export const setLoginTestStatus = (status) => {
  return { type: SET_LOGIN_TEST_ACTIVE, payload: status }
}

export const setLoginTestAccountName = (accountName) => {
  return { type: SET_LOGIN_TEST_ACCOUNT_NAME, payload: accountName }
}

export const setLoginTestAccountPasswd = (accountPasswd) => {
  return { type: SET_LOGIN_TEST_ACCOUNT_PASSWD, payload: accountPasswd }
}

export const setLoginTestInterval = (interval) => {
  return { type: SET_LOGIN_TEST_INTERVAL, payload: interval }
}

export const sendLoginTestDataAction = () => {
  return { type: SEND_LOGIN_TEST_DATA }
}

export const savedLoginTestAction = (payload) => {
  return { type: SAVED_LOGGING_TEST, payload: payload }
}

export const saveLoginTestFailedAction = (payload) => {
  return { type: SAVE_LOGGING_TEST_FAILED, payload: payload }
}

export const loadedLoginTestDataAction = (payload = {}) => {
  return { type: LOADED_LOGGING_TEST_DATA, payload: payload }
}

export const loginTestLogAction = (logLines = []) => {
  return { type: LOGGING_TEST_LOG, payload: logLines }
}

export default loginTest;
