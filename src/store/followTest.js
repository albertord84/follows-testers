import { assign } from "lodash-es";

const SEARCHING_FOLLOW_PROFILE = 'SEARCHING_FOLLOW_PROFILE';
const GET_USER_PROFILES = 'GET_USER_PROFILES';
const HIDE_USER_SEARCH_RESULT = 'HIDE_USER_SEARCH_RESULT';
const GOT_USER_PROFILES = 'GOT_USER_PROFILES';
const GET_USER_PROFILES_FAILED = 'GET_USER_PROFILES_FAILED';
const SET_USER_TO_FOLLOW = 'SET_USER_TO_FOLLOW';
const SEND_FOLLOW_USER_REQUEST = 'SEND_FOLLOW_USER_REQUEST';
const FOLLOW_USER_FAILED = 'FOLLOW_USER_FAILED';
const USER_FOLLOWED_SUCCESSFULLY = 'USER_FOLLOWED_SUCCESSFULLY';
const GOT_USER_FOLLOWING_LIST = 'GOT_USER_FOLLOWING_LIST';
const GOT_USER_FOLLOWERS_LIST = 'GOT_USER_FOLLOWERS_LIST';
const CLEAR_FOLLOWING_LIST = 'CLEAR_FOLLOWING_LIST';
const CLEAR_FOLLOWERS_LIST = 'CLEAR_FOLLOWERS_LIST';
const SET_FOLLOW_TEST_USERNAME = 'SET_FOLLOW_TEST_USERNAME';
const SET_FOLLOW_TEST_PASSWORD = 'SET_FOLLOW_TEST_PASSWORD';

const followTestState = {
  waiting: false,
  userName: '',
  password: '',
  users: [],
  followSelected: null,
  followers: [],
  following: [],
  userSearchError: '',
  followError: ''
}

const followTest = (state = followTestState, action) => {
  switch (action.type) {
    case SEARCHING_FOLLOW_PROFILE:
      return assign({}, state, { waiting: true });
  
    case GET_USER_PROFILES:
      return assign({}, state, {
        users: [],
        userSearchError: ''
      });
  
    case HIDE_USER_SEARCH_RESULT:
      return assign({}, state, { users: [] });
  
    case GOT_USER_PROFILES:
      return assign({}, state, {
        waiting: false,
        users: action.payload,
      });
  
    case GET_USER_PROFILES_FAILED:
      return assign({}, state, {
        waiting: false,
        userSearchError: action.payload
      });
  
    case SET_USER_TO_FOLLOW:
      return assign({}, state, {
        followSelected: action.payload
      });
  
    case SEND_FOLLOW_USER_REQUEST:
      return assign({}, state, {
        users: []
      });
  
    case FOLLOW_USER_FAILED:
      return assign({}, state, {
        waiting: false,
        followError: action.payload
      });
  
    case USER_FOLLOWED_SUCCESSFULLY:
      return assign({}, state, { waiting: false });
  
    case GOT_USER_FOLLOWING_LIST:
      return assign({}, state, { following: action.payload });
  
    case GOT_USER_FOLLOWERS_LIST:
      return assign({}, state, { followers: action.payload });
  
    case CLEAR_FOLLOWING_LIST:
      return assign({}, state, { following: [] });
  
    case CLEAR_FOLLOWERS_LIST:
      return assign({}, state, { followers: [] });
  
    case SET_FOLLOW_TEST_USERNAME:
      return assign({}, state, { userName: action.payload });
  
    case SET_FOLLOW_TEST_PASSWORD:
      return assign({}, state, { password: action.payload });
  
    default:
      return state;
  }
}

export const hideUserSearchResult = () => {
  return { type: HIDE_USER_SEARCH_RESULT }
}

export const sendFollowUserRequest = () => {
  return { type: SEND_FOLLOW_USER_REQUEST }
}

export const setFollowTestUserAction = (user) => {
  return { type: SET_FOLLOW_TEST_USERNAME, payload: user }
}

export const setFollowTestPasswordAction = (password) => {
  return { type: SET_FOLLOW_TEST_PASSWORD, payload: password }
}

export const clearFollowingListAction = () => {
  return { type: CLEAR_FOLLOWING_LIST }
}

export const clearFollowersListAction = () => {
  return { type: CLEAR_FOLLOWERS_LIST }
}

export const setFollowingListAction = (list) => {
  return { type: GOT_USER_FOLLOWING_LIST, payload: list }
}

export const setFollowersListAction = (list) => {
  return { type: GOT_USER_FOLLOWERS_LIST, payload: list }
}

export const followUserFailedAction = (error) => {
  return { type: FOLLOW_USER_FAILED, payload: error }
}

export const followUserSuccessAction = () => {
  return { type: USER_FOLLOWED_SUCCESSFULLY }
}

export const setUserToFollowAction = (user) => {
  return { type: SET_USER_TO_FOLLOW, payload: user }
}

export const getUserProfilesAction = () => {
  return { type: GET_USER_PROFILES }
}

export const gotUserProfilesAction = (users) => {
  return { type: GOT_USER_PROFILES, payload: users }
}

export const getProfilesFailedAction = (error) => {
  return { type: GET_USER_PROFILES_FAILED, payload: error }
}

export const searchFollowProfileAction = (payload) => {
  return { type: SEARCHING_FOLLOW_PROFILE, payload: payload }
}

export default followTest;
