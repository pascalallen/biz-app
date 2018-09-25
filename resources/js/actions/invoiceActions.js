import axios from "axios/index";

export function fetchSingleInvoice(endpoint, params = {}){
  return function(dispatch) {
    dispatch({type: "FETCH_SINGLE_INVOICE"});
    axios.get(endpoint, {
      params: params
    })
    .then((response) => {
      dispatch({
        type: "FETCH_SINGLE_INVOICE_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_SINGLE_INVOICE_REJECTED", payload: err})
    })
  }
}

export function fetchAllInvoices(endpoint, optionalParams = {}) {
  return function(dispatch) {
    dispatch({type: "FETCH_ALL_INVOICES"});
    axios.get(endpoint, {
      params: optionalParams
    })
    .then((response) => {
      dispatch({
        type: "FETCH_ALL_INVOICES_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "FETCH_ALL_INVOICES_REJECTED", payload: err})
    })
  }
}
