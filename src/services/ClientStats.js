import Axios from "axios";
import * as NProgress from "nprogress/nprogress";

import { Subject } from 'rxjs/Subject';
import { of } from "rxjs";

import { trim, includes, split } from 'lodash-es';

import { fromPromise } from 'rxjs/observable/fromPromise';
import { map as map$, switchMap, delay, tap, catchError, filter as filter$ } from "rxjs/operators";

import store, { getStatsServer, getStatsPage, getStatsPeriod } from "../store/index";
import { setStatsServer, setStatDates, setClientStats, setStatsPeriod, setStatsPage, setTotalStats, incStatsPage, decStatsPage } from "../store/clientStats";

export const serverSelect$ = new Subject();
export const logDateSelect$ = new Subject();
export const pageStatsClick$ = new Subject();
export const filterUserStatsKeystroke$ = new Subject();

serverSelect$.pipe(
    map$(inputEl => inputEl.getAttribute('value')),
    tap(server => store.dispatch(setStatsServer(server))),
    tap(() => store.dispatch(setStatDates([]))),
    tap(() => store.dispatch(setClientStats([]))),
    tap(() => NProgress.start()),
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
    delay(200),
    switchMap(period => {
        const searchUrl = `${global.baseUrl}/stats/users/${getStatsServer()}/${period}/${getStatsPage()}`;
        const promise = Axios.post(searchUrl)
        .then(response => {
            return response.data;
        });
        return fromPromise(promise);
    }),
    catchError(error => {
        if (error.message) { console.log(`Error obteniendo trazas por usuario: ${error.message}`); }
        return of([]);
    }),
    delay(500),
    map$(resp => resp.data), // para evitar response.data.data en el then de la promise
    tap(data => store.dispatch(setStatsPage(data.page))),
    tap(data => store.dispatch(setTotalStats(data.total))),
    tap(() => NProgress.done()),
    tap(() => console.log(`loaded user stats for server ${getStatsServer()}`)),
    map$(data => data.stats)
).subscribe(stats => {
    store.dispatch(setClientStats(stats));
});

pageStatsClick$.pipe(
    map$(spanEl => spanEl.getAttribute('class')),
    tap(className => {
        if (includes(className, 'more-stats')) {
            store.dispatch(incStatsPage());
            return;
        }
        store.dispatch(decStatsPage());
    }),
    tap(() => NProgress.start()),
    delay(200),
    switchMap(period => {
        const searchUrl = `${global.baseUrl}/stats/users/${getStatsServer()}/${getStatsPeriod()}/${getStatsPage()}`;
        const promise = Axios.post(searchUrl)
        .then(response => {
            return response.data;
        });
        return fromPromise(promise);
    }),
    catchError(error => {
        if (error.message) { console.log(`Error obteniendo trazas por usuario: ${error.message}`); }
        return of([]);
    }),
    delay(500),
    map$(resp => resp.data), // para evitar response.data.data en el then de la promise
    tap(data => store.dispatch(setStatsPage(data.page))),
    tap(data => store.dispatch(setTotalStats(data.total))),
    tap(() => NProgress.done()),
    tap(() => console.log(`loaded user stats for server ${getStatsServer()}`)),
    map$(data => data.stats)
).subscribe(stats => {
    store.dispatch(setClientStats(stats));
});

filterUserStatsKeystroke$.pipe(
    filter$(ev => ev.keyCode === 13),
    map$(ev => ev.target.value),
    map$(value => split(value, ' ').shift()),
    tap(() => NProgress.start()),
    delay(200),
    switchMap(client => {
        const searchUrl = `${global.baseUrl}/stats/user/${client}/${getStatsServer()}/${getStatsPeriod()}`;
        const promise = Axios.post(searchUrl)
            .then(response => {
                return response.data;
            });
        return fromPromise(promise);
    }),
    catchError(error => {
        if (error.message) {
            console.log(`Error obteniendo trazas por usuario: ${error.message}`);
        }
        return of([]);
    }),
    delay(500),
    map$(resp => resp.data), // para evitar response.data.data en el then de la promise
    tap(data => store.dispatch(setTotalStats(data.total))),
    tap(() => NProgress.done()),
    tap(() => console.log(`loaded stats for selected user`)),
    map$(data => data.stats)
).subscribe(stats => {
	store.dispatch(setClientStats(stats));
});
