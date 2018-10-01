import axios from "axios/index";

export function fetchAuth(endpoint, params = {}){
  return function(dispatch) {
    dispatch({type: "FETCH_AUTH"});
    axios.get(endpoint, {
      params: params
    })
    .then((response) => {
      dispatch({
        type: "FETCH_AUTH_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_AUTH_REJECTED", payload: err})
    })
  }
}
