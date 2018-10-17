import axios from "axios/index";

export function uploadFile(endpoint, params = {}){
  return function(dispatch) {
    dispatch({type: "UPLOAD_FILE"});

    const formData = new FormData();

    params.files.map((file,i) => {
        formData.append(`file_${i}`, file);
    })
    formData.append('invoice_key', params.invoice_key);
    formData.append('customer_key', params.customer_key);
    formData.append('customer_name', params.customer_name);

    const config = { headers: { 'Content-Type': 'multipart/form-data' } };

    axios.post(endpoint, formData, config)
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

export function downloadFiles(endpoint, params = {}){
    return function(dispatch) {
      dispatch({type: "DOWNLOAD_FILES"});

      const formData = new FormData();

      params.files.map((file,i) => {
          formData.append(`file_${i}`, file);
      })
      formData.append('invoice_key', params.invoice_key);
      formData.append('customer_key', params.customer_key);
      formData.append('customer_name', params.customer_name);

      const config = { headers: { 'Content-Type': 'multipart/form-data' } };

      axios.post(endpoint, formData, config)
      .then((response) => {
        dispatch({
          type: "DOWNLOAD_FILES_FULFILLED",
          payload: response.data
        })
      })
      .catch((err) => {
        dispatch({type: "DOWNLOAD_FILES_REJECTED", payload: err})
      })
    }
  }
