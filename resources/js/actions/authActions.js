import axios from "axios/index";

export function fetchAuthUser(endpoint, params = {}){
  return function(dispatch) {
    dispatch({type: "FETCH_AUTH_USER"});
    axios.get(endpoint, {
      params: params
    })
    .then((response) => {
      dispatch({
        type: "FETCH_AUTH_USER_FULFILLED",
        payload: response
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_AUTH_USER_REJECTED", payload: err})
    })
  }
}
