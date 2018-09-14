import ReactDOM from "react-dom";
import React from 'react';
import Main from './components/Main';
import { Provider } from 'react-redux';
import store from "./store"


require('./bootstrap');

const app = document.getElementById('root');

    ReactDOM.render(
        <Provider store={store}>
            <Main />
        </Provider>
        , app);
