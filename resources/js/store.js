import { applyMiddleware, createStore } from 'redux'

import logger from 'redux-logger'
import thunk from 'redux-thunk'
import promise from 'redux-promise-middleware'

import reducer from './reducers'

let middleware = [promise(), thunk];

if (process.env.NODE_ENV === 'development') {
    middleware = [...middleware, logger];
}

export default createStore(reducer, applyMiddleware(...middleware))
