import Axios from "axios";
import * as NProgress from "nprogress/nprogress";

import {
  trim, split, filter as $filter, isString
} from "lodash-es";

import { Subject } from 'rxjs/Subject';
import { of } from "rxjs";

import { fromPromise } from 'rxjs/observable/fromPromise';
import {
  filter, map, switchMap, delay, tap, catchError,
  debounceTime, distinctUntilChanged
} from "rxjs/operators";

import store, {
  validFollowAccountCreds, followRequestData,
  getFollowUsersList, followAccountCreds
} from "../store/index";

import {
  gotUserProfilesAction, setUserToFollowAction,
  followUserSuccessAction, setFollowingListAction,
  clearFollowingListAction, setFollowTestUserAction,
  setFollowTestPasswordAction, sendFollowUserRequest,
  clearFollowersListAction, setFollowersListAction,
  getUserProfilesAction, hideUserSearchResult
} from "../store/followTest";

// remote search of users matching typed query

export const searchUserInput$ = new Subject();

searchUserInput$.pipe(
  debounceTime(500),
  distinctUntilChanged(),
  map(query => trim(query)),
  map(trimmed => split(trimmed, ' ').shift()),
  filter(firstWord => firstWord.length >= 3),
  tap(() => NProgress.start()),
  tap(() => store.dispatch(getUserProfilesAction())),
  delay(200),
  tap(() => NProgress.set(0.45)),
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
).subscribe(searchResult => {
  store.dispatch(gotUserProfilesAction(searchResult));
});

// follow user actions

export const onFollowUser$ = new Subject();

const PROVIDE_CREDENTIALS = 'You must first provide Instagram username/password';

onFollowUser$.pipe(
  map(name => $filter(getFollowUsersList(), { username: name }).shift()),
  map(selectedUser => validFollowAccountCreds() ? selectedUser : PROVIDE_CREDENTIALS),
  tap(mappedData => {
    if (isString(mappedData)) { alert(mappedData); }
  }),
  filter(mappedData => isString(mappedData) ? false : mappedData),
  tap(selectedUser => store.dispatch(setUserToFollowAction(selectedUser))),
  tap(() => NProgress.start()),
  tap(() => store.dispatch(sendFollowUserRequest())),
  tap(() => store.dispatch(hideUserSearchResult())),
  switchMap(() => {
    const followUrl = `${global.baseUrl}/user/follow`;
    const promise = Axios.post(followUrl, followRequestData())
    .then(response => {
      return response.data;
    });
    return fromPromise(promise).pipe(
      catchError(() => of(false))
    );
  }),
  tap(() => NProgress.set(0.75)),
  delay(200),
  filter(searchResult => searchResult !== false),
  tap(() => NProgress.done())
).subscribe(() => {
  store.dispatch(followUserSuccessAction());
});

// search list of people that the instagram account is following

export const followingSearch$ = new Subject();

followingSearch$.pipe(
  filter(value => validFollowAccountCreds() ? value : false),
  debounceTime(500),
  distinctUntilChanged(),
  map(value => trim(value)),
  filter(value => value.length >= 3),
  tap(() => NProgress.start()),
  tap(() => store.dispatch(clearFollowingListAction())),
  switchMap(query => {
    const followingSearchUrl = `${global.baseUrl}/user/following/${query}`;
    const promise = Axios.post(followingSearchUrl, followAccountCreds())
    .then(response => response.data.users)
    return fromPromise(promise).pipe(
      catchError(() => of(false))
    );
  }),
  tap(() => NProgress.set(0.75)),
  delay(200),
  filter(searchResult => searchResult !== false),
  tap(() => NProgress.done())
).subscribe((userList) => {
  store.dispatch(setFollowingListAction(userList));
});

// instagram user account to be able to query remote servers

export const credentialChange$ = new Subject();

credentialChange$.pipe(
  filter(input => input.type === 'text')
).subscribe(input => {
  store.dispatch(setFollowTestUserAction(input.value))
});

credentialChange$.pipe(
  filter(input => input.type === 'password')
)
.subscribe(input => {
  store.dispatch(setFollowTestPasswordAction(input.value));
});

// search among the list of followers of the instagram account

export const followerSearch$ = new Subject();

followerSearch$.pipe(
  filter(value => validFollowAccountCreds() ? value : false),
  debounceTime(500),
  distinctUntilChanged(),
  map(value => trim(value)),
  filter(value => value.length >= 3),
  tap(() => NProgress.start()),
  delay(300),
  tap(() => NProgress.set(0.35)),
  tap(() => store.dispatch(clearFollowersListAction())),
  switchMap(query => {
    const followersSearchUrl = `${global.baseUrl}/user/followers/${query}`;
    const promise = Axios.post(followersSearchUrl, followAccountCreds())
    .then(response => {
      return response.data.users;
    });
    return fromPromise(promise).pipe(
      catchError(() => of(false))
    );
  }),
  tap(() => NProgress.set(0.75)),
  delay(200),
  filter(followersList => followersList !== false),
  tap(() => NProgress.done())
).subscribe(followersList => {
  store.dispatch(setFollowersListAction(followersList));
});
