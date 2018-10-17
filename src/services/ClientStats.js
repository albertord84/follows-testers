import Axios from "axios";
import * as NProgress from "nprogress/nprogress";

import { Subject } from 'rxjs/Subject';
import { of } from "rxjs";

import { trim } from 'lodash-es';

import { fromPromise } from 'rxjs/observable/fromPromise';
import {
    filter as filter$, map as map$, switchMap, delay, tap, catchError
} from "rxjs/operators";

import store, { getStatsServer, getStatsPage } from "../store/index";
import { setStatsServer, setStatDates, setClientStats, setStatsPeriod } from "../store/clientStats";

export const serverSelect$ = new Subject();
export const logDateSelect$ = new Subject();

serverSelect$.pipe(
    map$(inputEl => inputEl.getAttribute('value')),
    tap(server => store.dispatch(setStatsServer(server))),
    tap(() => store.dispatch(setStatDates([]))),
    tap(() => NProgress.start()),
    tap(() => NProgress.set(0.3)),
    delay(200),
    switchMap(server => {
        const searchUrl = `${global.baseUrl}/stats/server/${server}`;
        const promise = Axios.post(searchUrl)
        .then(response => {
            return response.data.stats;
        });
        return fromPromise(promise);
    }),
    catchError(error => {
        if (error.message) { console.log(`Error obteniendo listado de trazas: ${error.message}`); }
        return of([]);
    }),
    delay(500),
    tap(() => NProgress.done()),
    tap(() => console.log(`loaded log dates for server ${getStatsServer()}`))
).subscribe(servers => {
    store.dispatch(setStatDates(servers));
});

logDateSelect$.pipe(
    map$(spanEl => trim(spanEl.innerHTML)),
    tap(period => store.dispatch(setStatsPeriod(period))),
    tap(() => store.dispatch(setClientStats([]))),
    tap(() => NProgress.start()),
    tap(() => NProgress.set(0.3)),
    delay(200),
    switchMap(period => {
        const searchUrl = `${global.baseUrl}/stats/users/${getStatsServer()}/${period}/${getStatsPage()}`;
        const promise = Axios.post(searchUrl)
        .then(response => {
            return response.data.stats;
        });
        return fromPromise(promise);
    }),
    catchError(error => {
        if (error.message) { console.log(`Error obteniendo trazas por usuario: ${error.message}`); }
        return of([]);
    }),
    delay(500),
    tap(() => NProgress.done()),
    tap(() => console.log(`loaded user stats for server ${getStatsServer()}`))
).subscribe(stats => {
    store.dispatch(setClientStats(stats));
});
