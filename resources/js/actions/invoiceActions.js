import axios from "axios/index";

export function fetchSingle(endpoint, params = {}){
  return function(dispatch) {
    dispatch({type: "FETCH_SINGLE"});
    axios.get(endpoint, {
      params: params
    })
    .then((response) => {
        console.log(response)
      dispatch({
        type: "FETCH_SINGLE_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_SINGLE_REJECTED", payload: err})
    })
  }
}

export function fetchAll(endpoint, optionalParams = {}) {
  return function(dispatch) {
    dispatch({type: "FETCH_ALL"});
    axios.get(endpoint, {
      params: optionalParams
    })
    .then((response) => {
      dispatch({
        type: "FETCH_ALL_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_ALL_REJECTED", payload: err})
    })
  }
}
