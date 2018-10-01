export default function reducer(state={
    authUser: [],
    fetchedAuthUser: false,
    error: null,
    fetchingAuthUser: false,
  }, action) {

    switch (action.type) {

      case "FETCH_AUTH_USER": {
        return {...state, fetchingAuthUser: true }
      }
      case "FETCH_AUTH_USER_REJECTED": {
        return {...state, fetchingAuthUser: false, error: action.payload}
      }
      case "FETCH_AUTH_USER_FULFILLED": {
        return {
          ...state,
          fetchingAuthUser: false,
          fetchedAuthUser: true,
          authUser: action.payload,
        }
      }

    }

    return state
  }
