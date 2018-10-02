export default function reducer(state={
    file: [],
    uploadedFile: false,
    error: null,
    uploadingFile: false,
  }, action) {

    switch (action.type) {

      case "UPLOAD_FILE": {
        return {...state, uploadingFile: true }
      }
      case "UPLOAD_FILE_REJECTED": {
        return {...state, uploadingFile: false, error: action.payload}
      }
      case "UPLOAD_FILE_FULFILLED": {
        return {
          ...state,
          uploadingFile: false,
          uploadedFile: true,
          file: action.payload.data,
        }
      }

    }

    return state
  }
