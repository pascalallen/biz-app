import { combineReducers } from 'redux'
import { reducer as form } from 'redux-form'

import account from './accountReducer'
import customer from './customerReducer'
import invoice from './invoiceReducer'

export default combineReducers({
    account,
    customer,
    invoice,
})
