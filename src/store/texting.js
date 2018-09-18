import { assign } from "lodash-es";

const SET_DIRECT_MESSAGE_TEXT = 'SET_DIRECT_MESSAGE_TEXT';
const SET_DIRECT_REFERENCE_PROFILE = 'SET_DIRECT_REFERENCE_PROFILE';
const SET_REF_PROFS_SEARCH_LIST = 'SET_REF_PROFS_SEARCH_LIST';

const textingState = {
  message: '',
  profile: null,
  searchList: []
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

export default texting;
