import Axios from "axios";
import * as NProgress from "nprogress/nprogress";

import {
  trim, split, filter as $filter, isString
} from "lodash-es";

import { Subject } from 'rxjs/Subject';
import { of } from "rxjs";

import { fromPromise } from 'rxjs/observable/fromPromise';
import {
  filter as filter$, map as map$, switchMap, delay, tap, catchError,
  debounceTime, distinctUntilChanged
} from "rxjs/operators";

import store from "../store/index";
import { setStatsServer } from "../store/clientStats";

export const serverSelect$ = new Subject();

serverSelect$.pipe(
    map$(inputEl => inputEl.getAttribute('value'))
).subscribe(server => store.dispatch(setStatsServer(server)));
