import Axios from "axios";
import { lowerCase, includes } from "lodash-es";

import * as NProgress from "nprogress/nprogress";

import store, {
  loginTestData
} from "../store/index";

import {
  sendLoginTestDataAction, savedLoginTestAction,
  saveLoginTestFailedAction, loadedLoginTestDataAction,
  loginTestLogAction, setLoginTestAccountName,
  setLoginTestAccountPasswd, setLoginTestInterval,
  setLoginTestStatus
} from "../store/loginTest";

import { of } from "rxjs";
import { fromPromise } from 'rxjs/observable/fromPromise';
import { filter, map, switchMap, delay, tap, catchError } from "rxjs/operators";

export const loginTestInput$ = (targetEl) => {
  const $input = of(targetEl);

  $input.pipe(
    filter(el => includes(el.id, 'user')),
    map(el => el.value)
  ).subscribe(userName => {
    store.dispatch(setLoginTestAccountName(userName));
  });

  $input.pipe(
    filter(el => includes(el.id, 'pass')),
    map(el => el.value)
  ).subscribe(password => {
    store.dispatch(setLoginTestAccountPasswd(password));
  });

  $input.pipe(
    filter(el => includes(el.id, 'interv')),
    map(el => el.value)
  ).subscribe(interval => {
    store.dispatch(setLoginTestInterval(interval));
  });
}

export const loginTestActivated$ = (targetEl) => {
  const radioClicked$ = of(targetEl);

  radioClicked$.pipe(
    filter(el => lowerCase(el.tagName)==='small'),
    map(el => el.previousElementSibling),
    map(radio => radio.value)
  ).subscribe(status => {
    store.dispatch(setLoginTestStatus(status));
  });

  radioClicked$.pipe(
    filter(el => lowerCase(el.tagName)==='label'),
    map(el => el.querySelector('input')),
    map(radio => radio.value)
  ).subscribe(status => {
    store.dispatch(setLoginTestStatus(status));
  });

}

export const saveLoginTestData$ = (ev) => {
  ev.preventDefault();
  of(ev.target).pipe(
    map(() => loginTestData()),
    tap(() => NProgress.start()),
    tap(() => store.dispatch(sendLoginTestDataAction())),
    switchMap(loginTestData => {
      const promise = Axios.post(`${global.baseUrl}/login/save`, loginTestData)
      .then(response => {
        return response.data;
      })
      return loginTestData ? fromPromise(promise) : of(false);
    }),
    tap(() => NProgress.set(0.7)),
    catchError(error => {
      const err = error.response.statusText;
      store.dispatch(saveLoginTestFailedAction({ error: err }));
      console.error(err);
      NProgress.done();
      return of(false);
    }),
    delay(1500),
    filter(loginTestSaved => loginTestSaved !== false),
    tap(() => NProgress.done())
  ).subscribe(respData => store.dispatch(savedLoginTestAction(respData)))
}

export const loadLoginTestState = () => {
  Axios.get(`${global.baseUrl}/login/load`, {
    token: store.getState().user.token
  })
  .then(response => {
    setTimeout(function() {
      store.dispatch(loadedLoginTestDataAction(response.data));
    }, 1000);
  })
  .catch(error => {
    setTimeout(function() {
      store.dispatch(loadedLoginTestDataAction());
      console.error(error.response.statusText);
    }, 1000);
  });
}

export const lastLoginTestLog = () => {
  Axios.get(`${global.baseUrl}/login/log`, {
    token: store.getState().user.token
  })
  .then(response => {
    setTimeout(function() {
      const logLines = response.data.log;
      store.dispatch(loginTestLogAction(logLines));
    }, 3000);
  })
  .catch(error => {
    setTimeout(function() {
      console.error(error.response.statusText);
    }, 1000);
  });
}

export const refreshLoginTestLog = () => {
  store.dispatch(loginTestLogAction());
  lastLoginTestLog();
}

export const execLoginNow = () => {
  NProgress.start();
	const promise = Axios.get(`${global.baseUrl}/login/cron/true`)
  .then(response => {
    NProgress.done();
    console.log(response.data);
  })
  .catch(error => {
    setTimeout(function () {
      console.error(error.response.statusText);
      NProgress.done();
    }, 1000);
  });
}
