import { createStore, combineReducers } from "redux";
import { assign, filter, isEmpty, trim, isNaN } from "lodash-es";

import user from "./user";
import loginTest from "./loginTest";
import followTest from "./followTest";
import texting from "./texting";
import clientStats from "./clientStats";

const reducer = combineReducers({
    user, loginTest, followTest, texting, clientStats
});

const store = createStore(reducer);
global.store = process.env.NODE_ENV === 'development' ? store : false;

// login selectors

export const isLogged = () => store.getState().user.logged;
export const getLoginName = () => store.getState().user.userName;
export const getLoginPasswd = () => store.getState().user.password;
export const validLoginCreds = () => !isEmpty(getLoginName()) && !isEmpty(getLoginPasswd());
export const loginCreds = () => assign({}, {
    userName: getLoginName(), password: getLoginPasswd()
});

// login test selectors

export const loginTestUser = () => store.getState().loginTest.userName;
export const loginTestPasswd = () => store.getState().loginTest.password;
export const loginTestInterval = () => {
    const i = trim(store.getState().loginTest.interval);
    if (isNaN(i)) { return 1; }
    return i;
}
export const loginTestStatus = () => {
    const status = trim(store.getState().loginTest.activated);
    return status === '' ? 'off' : status;
}
export const loginTestData = () => assign({}, {
    userName: loginTestUser(),
    password: loginTestPasswd(),
    interval: loginTestInterval(),
    activated: loginTestStatus()
});

// follow and unfollow test selectors

export const getFollowTest = () => store.getState().followTest;
export const getFollowUsersList = () => store.getState().followTest.users;
export const getFollowUserByName = (name) => filter(getFollowUsersList(), { username: name }).shift();
export const followAccountCreds = () => assign({}, { userName: getFollowTest().userName, password: getFollowTest().password });
export const followAccountUserName = () => followAccountCreds().userName;
export const followAccountPassword = () => followAccountCreds().password;
export const validFollowAccountCreds = () => !isEmpty(followAccountUserName()) && !isEmpty(followAccountPassword());
export const selectedInstagramUserId = () => store.getState().followTest.followSelected.pk;
export const followRequestData = () => assign({}, {
    userName: followAccountUserName(), password: followAccountPassword(),
    userId: selectedInstagramUserId()
});

// direct texting selectors

export const getDirectProfSearchList = () => store.getState().texting.searchList;
export const getRefProfileFromSearchList = (profileId) => filter(getDirectProfSearchList(), { pk: profileId }).shift();
export const getSelectedReferenceProfile = () => store.getState().texting.profile;
export const getReferenceProfileId = () => getSelectedReferenceProfile().pk;
export const getDirectMsgText = () => trim(store.getState().texting.message);
export const getDirectDataToSend = () => assign({}, {
    userName: getLoginName(),
    password: getLoginPasswd(),
    message: getDirectMsgText(),
    profileId: getReferenceProfileId()
});
export const loadedDeliveryStats = () => store.getState().texting.loadDeliveryStats;
export const loadedDirectMessages = () => store.getState().texting.loadDirectMessages;
export const getDeliveryLog = () => store.getState().texting.deliveryLog;
export const getDirectMessages = () => store.getState().texting.messages;
export const getStatsServer = () => store.getState().clientStats.server;
export const getStatsClientName = () => store.getState().clientStats.clientName;
export const getStatsClientId = () => store.getState().clientStats.clientId;
export const getStatsPeriod = () => store.getState().clientStats.period;
export const getClientStats = () => store.getState().clientStats.stats;
export const getStatDates = () => store.getState().clientStats.dates;
export const getStatsPage = () => store.getState().clientStats.page;

export default store;
