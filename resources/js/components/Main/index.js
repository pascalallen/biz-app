import React from 'react';
import {connect} from 'react-redux';
import moment from 'moment';
import {fetchAllInvoices, fetchSingleInvoice} from '../../actions/invoiceActions';
import {fetchAllCustomers} from '../../actions/customerActions';
import {fetchAuth} from '../../actions/authActions';
import { ListGroup, ListGroupItem, Button } from 'react-bootstrap';
import { CenterRow, TooltipDiv, FloatRight } from './styles';
import Loading from '../Loading';

const mapStateToProps = (state) => ({
  customer: state.customer,
  invoice: state.invoice,
  auth: state.auth,
});

class Main extends React.Component {
  constructor (props) {
    super(props);
    this.state = {
      startDate: moment().subtract(1, 'days'),
      endDate: moment(),
    };

    this.getCustomerInvoices = this.getCustomerInvoices.bind(this);
  }

  getAllCustomers(){
    this.props.fetchAllCustomers(`/api/customers`, {
      from: this.state.startDate.format("YYYY-MM-DD"),
      to: this.state.endDate.format("YYYY-MM-DD")
    });
  }

  getSingleInvoice(event){
    this.props.fetchSingleInvoice(`/api/invoices/${event.target.value}`, {
      from: this.state.startDate.format("YYYY-MM-DD"),
      to: this.state.endDate.format("YYYY-MM-DD"),
    });
  }

  getCustomerInvoices(event){
    this.props.fetchAllInvoices(`/api/invoices`, {
      customer: event.target.value,
    });
  }

  componentDidMount() {
    this.getAllCustomers();
    this.props.fetchAuth(`/api/user`);
  }

  render () {
    return (
    <div className="container">
        {this.props.auth.user.refresh_token ?
            <div className="row">
                {this.props.customer.fetchingAll ? <Loading />
                : <div className="col-6">
                    <h4>Customers</h4>
                    <ListGroup>
                    {this.props.customer.all && this.props.customer.all.map((customer,i) => (
                        <ListGroupItem key={i} value={customer.Id} onClick={this.getCustomerInvoices} className="d-flex justify-content-between align-items-center">
                        {customer.CompanyName ? customer.CompanyName
                            : customer.DisplayName ? customer.DisplayName
                            : customer.FullyQualifiedName}
                        {/* <span className="badge badge-primary badge-pill">14</span> */}
                        </ListGroupItem>
                    ))}
                    </ListGroup>
                </div>
                }
                {this.props.invoice.fetchingAll ? <Loading />
                : <div className="col-6">
                    <h4>Invoices</h4>
                    <ListGroup>
                    {this.props.invoice.all && this.props.invoice.all.map((invoice,i) => (
                        <ListGroupItem key={i} value={invoice.Id} className="d-flex justify-content-between align-items-center">
                        <p><strong>Invoice No.: </strong>{invoice.Id} <strong>Amount: </strong>{invoice.TotalAmt} <strong>Balance: </strong>{invoice.Balance} <strong>Due: </strong>{invoice.DueDate}</p>
                        </ListGroupItem>
                    ))}
                    </ListGroup>
                </div>
                }
            </div>
        : <Button bsStyle="link" href="connect-quickbooks">Connect Quickbooks</Button>}
    </div>
    )
  }
}

export default connect(
  mapStateToProps,
  {
    fetchAllCustomers,
    fetchSingleInvoice,
    fetchAllInvoices,
    fetchAuth,
  }
)(Main);
