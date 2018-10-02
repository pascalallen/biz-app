import axios from "axios/index";

export function uploadFile(endpoint, params = {}){
  return function(dispatch) {
    dispatch({type: "UPLOAD_FILE"});
    axios.post(endpoint, {
      params: params
    })
    .then((response) => {
      dispatch({
        type: "UPLOAD_FILE_FULFILLED",
        payload: response.data
      })
    })
    .catch((err) => {
      dispatch({type: "UPLOAD_FILE_REJECTED", payload: err})
    })
  }
}
