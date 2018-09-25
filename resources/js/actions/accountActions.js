import axios from "axios/index";

export function fetchSingleAccount(endpoint, params = {}){
  return function(dispatch) {
    dispatch({type: "FETCH_SINGLE_ACCOUNT"});
    axios.get(endpoint, {
      params: params
    })
    .then((response) => {
      dispatch({
        type: "FETCH_SINGLE_ACCOUNT_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_SINGLE_ACCOUNT_REJECTED", payload: err})
    })
  }
}

export function fetchAllAccounts(endpoint, optionalParams = {}) {
  return function(dispatch) {
    dispatch({type: "FETCH_ALL_ACCOUNTS"});
    axios.get(endpoint, {
      params: optionalParams
    })
    .then((response) => {
      dispatch({
        type: "FETCH_ALL_ACCOUNTS_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_ALL_ACCOUNTS_REJECTED", payload: err})
    })
  }
}
