export default function reducer(state={
    user: [],
    fetchedAuth: false,
    error: null,
    fetchingAuth: false,
  }, action) {

    switch (action.type) {

      case "FETCH_AUTH": {
        return {...state, fetchingAuth: true }
      }
      case "FETCH_AUTH_REJECTED": {
        return {...state, fetchingAuth: false, error: action.payload}
      }
      case "FETCH_AUTH_FULFILLED": {
        return {
          ...state,
          fetchingAuth: false,
          fetchedAuth: true,
          user: action.payload,
        }
      }

    }

    return state
  }
