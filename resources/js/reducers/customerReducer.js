export default function reducer(state={
    all: [],
    single: [],
    fetchedAll: false,
    fetchedSingle: false,
    error: null,
    fetchingAll: false,
    fetchingSingle: false,
  }, action) {

    switch (action.type) {

      case "FETCH_ALL": {
        return {...state, fetchingAll: true }
      }
      case "FETCH_ALL_REJECTED": {
        return {...state, fetchingAll: false, error: action.payload}
      }
      case "FETCH_ALL_FULFILLED": {
        return {
          ...state,
          fetchingAll: false,
          fetchedAll: true,
          all: action.payload.data,
        }
      }

      case "FETCH_SINGLE": {
        return {...state, fetchingSingle: true }
      }
      case "FETCH_SINGLE_REJECTED": {
        return {...state, fetchingSingle: false, error: action.payload}
      }
      case "FETCH_SINGLE_FULFILLED": {
        return {
          ...state,
          fetchingSingle: false,
          fetchedSingle: true,
          single: action.payload.data,
        }
      }

    }

    return state
  }
