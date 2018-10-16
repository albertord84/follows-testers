import { assign } from "lodash-es";

const STATS_SERVER_SELECT = 'STATS_SERVER_SELECT';
const SET_STATS_CLIENT = 'SET_STATS_CLIENT';
const SET_STATS_PERIOD = 'SET_STATS_PERIOD';
const SET_CLIENT_STATS = 'SET_CLIENT_STATS';
const SET_STAT_DATES = 'SET_STAT_DATES';

const clientStatState = {
    server: '',
    clientName: '',
    clientId: '',
    period: '',
    dates: [],
    stats: []
}

const clientStats = (state = clientStatState, action) => {
    switch (action.type) {
        case STATS_SERVER_SELECT: {
            return assign({}, state, {
                server: action.payload
            });
        }
        case SET_STATS_CLIENT: {
            return assign({}, state, {
                clientName: action.payload.clientName,
                clientId: action.payload.clientId
            });
        }
        case SET_STATS_PERIOD: {
            return assign({}, state, {
                period: action.payload
            });
        }
        case SET_CLIENT_STATS: {
            return assign({}, state, {
                stats: action.payload
            });
        }
        case SET_STAT_DATES: {
            return assign({}, state, {
                dates: action.payload
            });
        }
        default:
            return state;
    }
}

export const setStatsServer = (server) => {
    return {
        type: STATS_SERVER_SELECT, payload: server
    };
}

export const setStatsClient = (clientData) => {
    return {
        type: SET_STATS_CLIENT, payload: clientData
    };
}

export const setStatsPeriod = (period) => {
    return {
        type: SET_STATS_PERIOD, payload: period
    };
}

export const setClientStats = (stats) => {
    return {
        type: SET_CLIENT_STATS, payload: stats
    };
}

export const setStatDates = (dates) => {
    return {
        type: SET_STAT_DATES, payload: dates
    };
}

export default clientStats;
