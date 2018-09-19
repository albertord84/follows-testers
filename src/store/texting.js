import { assign } from "lodash-es";

const SET_DIRECT_MESSAGE_TEXT = 'SET_DIRECT_MESSAGE_TEXT';
const SET_DIRECT_REFERENCE_PROFILE = 'SET_DIRECT_REFERENCE_PROFILE';
const SET_REF_PROFS_SEARCH_LIST = 'SET_REF_PROFS_SEARCH_LIST';
const LOAD_DELIVERY_STATS = 'LOAD_DELIVERY_STATS';
const SET_DELIVERY_LOG = 'SET_DELIVERY_LOG';

const textingState = {
  message: '',
  profile: null,
  searchList: [],
  loadDeliveryStats: false,
  deliveryLog: []
};

export const texting = (state = textingState, action) => {
  switch (action.type) {
    case SET_DIRECT_MESSAGE_TEXT:
      return assign({}, state, {
        message: action.payload
      });

    case SET_DIRECT_REFERENCE_PROFILE:
      return assign({}, state, {
        profile: action.payload
      });

    case SET_REF_PROFS_SEARCH_LIST:
      return assign({}, state, {
        searchList: action.payload
      });
  
    case LOAD_DELIVERY_STATS:
      return assign({}, state, {
        loadDeliveryStats: action.payload
      });
  
    case SET_DELIVERY_LOG:
      return assign({}, state, {
        deliveryLog: action.payload
      });
  
    default:
      return state;
  }
}

export const setDirectMessageText = (message) => {
  return {
    type: SET_DIRECT_MESSAGE_TEXT, payload: message
  }
}

export const setDirectReferenceProfile = (profile = null) => {
  return {
    type: SET_DIRECT_REFERENCE_PROFILE, payload: profile
  }
}

export const setRefProfilesSearchList = (profilesList = []) => {
  return {
    type: SET_REF_PROFS_SEARCH_LIST, payload: profilesList
  }
}

export const loadDeliveryStats = (loadStats = false) => {
  return {
    type: LOAD_DELIVERY_STATS, payload: loadStats
  }
}

export const setDeliveryLog = (log = []) => {
  return {
    type: SET_DELIVERY_LOG, payload: log
  }
}

export default texting;
