import axios from "axios/index";

export function fetchSingleCustomer(endpoint, params = {}){
  return function(dispatch) {
    dispatch({type: "FETCH_SINGLE_CUSTOMER"});
    axios.get(endpoint, {
      params: params
    })
    .then((response) => {
      dispatch({
        type: "FETCH_SINGLE_CUSTOMER_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_SINGLE_CUSTOMER_REJECTED", payload: err})
    })
  }
}

export function fetchAllCustomers(endpoint, optionalParams = {}) {
  return function(dispatch) {
    dispatch({type: "FETCH_ALL_CUSTOMERS"});
    axios.get(endpoint, {
      params: optionalParams
    })
    .then((response) => {
      dispatch({
        type: "FETCH_ALL_CUSTOMERS_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_ALL_CUSTOMERS_REJECTED", payload: err})
    })
  }
}
