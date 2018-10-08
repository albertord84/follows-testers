import Axios from "axios";
import { trim, lowerCase, includes, split } from "lodash-es";

import * as NProgress from "nprogress/nprogress";

import store, { getRefProfileFromSearchList, getDirectMsgText,
  getDirectDataToSend, getSelectedReferenceProfile, getLoginName } from "../store/index";

import { of, Subject } from "rxjs";
import { fromPromise } from 'rxjs/observable/fromPromise';
import { fromEvent } from 'rxjs/observable/fromEvent';
import {
  filter, map, switchMap, delay, tap, catchError,
  distinctUntilChanged, debounceTime } from "rxjs/operators";
import { setRefProfilesSearchList, setDirectReferenceProfile,
  setDirectMessageText, loadDeliveryStats, setDeliveryLog, 
  setDirectMessages,
  loadDirectMessages} from "../store/texting";
import history, { atTexting } from "../history";

export const refProfileInput$ = new Subject();

// use case of disabling send button if no reference profile provided

refProfileInput$.pipe(
  map(val => trim(val)),
  filter(val => val.length < 4)
).subscribe(() => store.dispatch(setDirectReferenceProfile()));

// search profiles list while typing

refProfileInput$.pipe(
  map(val => trim(val)),
  filter(val => val.length > 3),
  distinctUntilChanged(),
  debounceTime(500),
  tap(() => NProgress.start()),
  tap(() => store.dispatch(setRefProfilesSearchList())),
  delay(100),
  tap(() => NProgress.set(0.3)),
  switchMap(query => {
    const searchUrl = `${global.baseUrl}/user/search/${query}`;
    const promise = Axios.post(searchUrl)
    .then(response => {
      return response.data.users;
    });
    return fromPromise(promise).pipe(
      catchError(() => of([]))
    );
  }),
  tap(() => NProgress.set(0.75)),
  delay(200),
  filter(searchResult => searchResult !== []),
  tap(() => NProgress.done())
).subscribe(result => store.dispatch(setRefProfilesSearchList(result)));

export const onRefProfileSelected$ = new Subject();

onRefProfileSelected$.pipe(
  tap(() => store.dispatch(setDirectReferenceProfile())),
  delay(200),
  map(profileId => getRefProfileFromSearchList(profileId)),
  tap(() => store.dispatch(setRefProfilesSearchList()))
).subscribe(result => store.dispatch(setDirectReferenceProfile(result)));

export const onDirectMessageTextInput$ = new Subject();

onDirectMessageTextInput$.pipe(
  map(text => trim(text))
).subscribe(text => store.dispatch(setDirectMessageText(text)));

export const onSendDirectData$ = new Subject();

onSendDirectData$.pipe(
  filter(() => !NProgress.isStarted()),
  filter(() => getDirectMsgText() !== ''),
  filter(() => getSelectedReferenceProfile() !== null),
  tap(() => NProgress.start()),
  map(() => getDirectDataToSend()),
  delay(500),
  tap(() => NProgress.set(0.5)),
  switchMap(directData => {
    const registerUrl = `${global.baseUrl}/texting/register`;
    const promise = Axios.post(registerUrl, directData)
    .then(response => response.data);
    return fromPromise(promise).pipe(
      catchError(error => {
        console.error(error);
        alert('The message was not registered. You might reached the limit of allowed messages.');
        return of(false);
      })
    );
  }),
  delay(1000),
  tap(() => NProgress.done()),
  filter(result => result !== false)
).subscribe(() => {
  alert('Your message was successfully registered.');
});

export const onCloseDeliveryStatsDialog$ = new Subject();
onCloseDeliveryStatsDialog$.subscribe(() => store.dispatch(loadDeliveryStats()));

export const onCloseMessagesDialog$ = new Subject();
onCloseMessagesDialog$.subscribe(() => store.dispatch(loadDirectMessages()));

const onToolBarButtonClick$ = fromEvent(document, 'click').pipe(
  filter(() => atTexting()),
  filter(ev => 'button' === lowerCase(ev.target.tagName)),
  map(ev => ev.target),
  map(btn => btn.getAttribute('class'))
);

onToolBarButtonClick$.pipe(
  filter(cls => includes(split(cls, ' '), 'delivery-log')),
  tap(() => NProgress.start()),
  delay(500),
  tap(() => NProgress.set(0.5)),
  switchMap(() => {
    const logUrl = `${global.baseUrl}/sender/delivery`;
    const promise = Axios.post(logUrl)
    .then(response => {
      return response.data.log;
    })
    return fromPromise(promise).pipe(
      catchError(error => {
        console.error(error);
        return of(false);
      })
    );
  }),
  delay(1000),
  tap(() => NProgress.done()),
  filter(result => result !== false),
  tap(result => store.dispatch(setDeliveryLog(result))),
  delay(500),
).subscribe(() => {
  store.dispatch(loadDeliveryStats(true));
});

onToolBarButtonClick$.pipe(
  filter(cls => includes(split(cls, ' '), 'active-messages')),
  tap(() => NProgress.start()),
  delay(200),
  tap(() => NProgress.set(0.5)),
  switchMap(() => {
    const logUrl = `${global.baseUrl}/sender/active-messages/${getLoginName()}`;
    const promise = Axios.post(logUrl)
    .then(response => {
      return response.data.messages;
    })
    return fromPromise(promise).pipe(
      catchError(error => {
        console.error(error);
        return of(false);
      })
    );
  }),
  delay(500),
  tap(() => NProgress.done()),
  filter(messages => messages !== false),
  tap(messages => store.dispatch(setDirectMessages(messages))),
  delay(100),
).subscribe(() => {
  store.dispatch(loadDirectMessages(true));
});

onToolBarButtonClick$.pipe(
  filter(cls => includes(split(cls, ' '), 'inactive-messages')),
  tap(() => NProgress.start()),
  delay(200),
  tap(() => NProgress.set(0.5)),
  switchMap(() => {
    const logUrl = `${global.baseUrl}/sender/inactive-messages/${getLoginName()}`;
    const promise = Axios.post(logUrl)
    .then(response => {
      return response.data.messages;
    })
    return fromPromise(promise).pipe(
      catchError(error => {
        console.error(error);
        return of(false);
      })
    );
  }),
  delay(500),
  tap(() => NProgress.done()),
  filter(messages => messages !== false),
  tap(messages => store.dispatch(setDirectMessages(messages))),
  delay(100),
).subscribe(() => {
  store.dispatch(loadDirectMessages(true));
});

