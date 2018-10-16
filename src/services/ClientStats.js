import Axios from "axios";
import * as NProgress from "nprogress/nprogress";

import { Subject } from 'rxjs/Subject';
import { of } from "rxjs";

import { fromPromise } from 'rxjs/observable/fromPromise';
import {
    filter as filter$, map as map$, switchMap, delay, tap, catchError
} from "rxjs/operators";

import store, { getStatsServer } from "../store/index";
import { setStatsServer, setStatsDates } from "../store/clientStats";

export const serverSelect$ = new Subject();

serverSelect$.pipe(
    map$(inputEl => inputEl.getAttribute('value')),
    tap(server => store.dispatch(setStatsServer(server))),
    tap(() => store.dispatch(setStatsDates([]))),
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
        return of([]);
    }),
    delay(500),
    tap(() => NProgress.done()),
    tap(() => console.log(`loaded log dates for server ${getStatsServer()}`))
).subscribe(servers => {
    store.dispatch(setStatsDates(servers));
});
