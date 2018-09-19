import { createHashHistory } from 'history'

const history = createHashHistory({
  hashType: 'slash'
});

const pathName = () => history.location.pathname;

export const atTexting = () => pathName().match(/^\/texting/) !== null;

export default history;