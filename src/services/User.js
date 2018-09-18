import Axios from "axios";

import * as NProgress from "nprogress/nprogress";

import store, {
  validLoginCreds, loginCreds
} from "../store/index"

import {
  loginAction, loggedAction, loginFailedAction,
  setLoginUserNameAction, setLoginPasswordAction,
} from "../store/user";

import history from "../history";

import { fromPromise } from 'rxjs/observable/fromPromise';
import { of } from "rxjs";

import {
  filter, map, switchMap, tap, catchError, delay
} from "rxjs/operators";

export const loginFormInput$ = (inputEl) => {
  const input$ = of(inputEl);

  input$.pipe(
    filter(el => el.type === 'text'),
    map(el => el.value)
  )
  .subscribe(val => store.dispatch(setLoginUserNameAction(val)))

  input$.pipe(
    filter(el => el.type === 'password'),
    map(el => el.value)
  )
  .subscribe(val => store.dispatch(setLoginPasswordAction(val)))
}

export const submitUserLogin$ = (formEl) => {
  const loginUrl = `${global.baseUrl}/user/signin`;
  of(formEl)
  .pipe(
    filter(() => validLoginCreds()),
    map(() => loginCreds()),
    tap(() => NProgress.start()),
    tap(() => store.dispatch(loginAction())),
    tap(() => NProgress.set(0.35)),
    delay(1000),
    switchMap(loginData => {
      const promise = Axios.post(loginUrl, loginData)
      .then(response => {
        return response.data;
      });
      return loginData ? fromPromise(promise) : of(false);
    }),
    tap(() => NProgress.set(0.7)),
    catchError(error => {
      const err = error.response.statusText;
      store.dispatch(loginFailedAction({ loginError: err }));
      console.error(err);
      NProgress.done();
      return of(false);
    }),
    delay(1000),
    filter(loginResp => loginResp !== false),
    tap(() => NProgress.done())
  ).subscribe(loginResult => {
    store.dispatch(loggedAction(loginResult));
    history.push('/home');
  });
}

export const redirectNotLogged = (isLogged) => {
  if (!isLogged) {
    history.push('/login');
  }
}

export const redirectHomeLogged = (isLogged) => {
  if (isLogged) {
    history.push('/home');
  }
}
