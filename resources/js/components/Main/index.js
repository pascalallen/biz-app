import React from 'react';
import {connect} from 'react-redux';
import moment from 'moment';
import {fetchAllInvoices, fetchSingleInvoice} from '../../actions/invoiceActions';
import {fetchAllCustomers} from '../../actions/customerActions';
import {fetchAuth} from '../../actions/authActions';
import {uploadFile} from '../../actions/fileActions';
import { ListGroup, ListGroupItem, Button } from 'react-bootstrap';
import { CenterRow, TooltipDiv, FloatRight, StyledDropzone } from './styles';
import Loading from '../Loading';

const mapStateToProps = (state) => ({
  customer: state.customer,
  invoice: state.invoice,
  auth: state.auth,
  file: state.file,
});

class Main extends React.Component {
  constructor (props) {
    super(props);
    this.state = {
      startDate: moment().subtract(1, 'days'),
      endDate: moment(),
      accepted: [],
      rejected: [],
      selectedCustomer: null,
    };

    this.getCustomerInvoices = this.getCustomerInvoices.bind(this);
  }

  componentDidMount() {
    this.getAllCustomers();
    this.props.fetchAuth(`/api/user`);
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

  getCustomerInvoices(customer, e){
    this.setState({selectedCustomer: customer.DisplayName});
    this.props.fetchAllInvoices(`/api/invoices`, {
      customer: customer.Id,
    });
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
                            <ListGroupItem key={i} onClick={(e) => this.getCustomerInvoices(customer, e)} className="d-flex justify-content-between align-items-center">
                                {customer.DisplayName}
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
                            <div className="col-8">
                                <ul className="list-group list-group-flush">
                                  <li className="list-group-item"><strong>Invoice No.: </strong>{invoice.Id}</li>
                                  <li className="list-group-item"><strong>Amount: </strong>{invoice.TotalAmt}</li>
                                  <li className="list-group-item"><strong>Balance: </strong>{invoice.Balance}</li>
                                  <li className="list-group-item"><strong>Due: </strong>{invoice.DueDate}</li>
                                </ul>
                            </div>
                            <div className="col-2">
                                {invoice.FileCount > 0 && <span className="badge badge-primary badge-pill">{invoice.FileCount}</span>}
                            </div>
                            <div className="col-2">
                                <StyledDropzone
                                    accept="image/*"
                                    onDrop={(accepted, rejected) => {
                                        this.props.uploadFile(`/api/files`, {
                                            invoice_key: invoice.Id,
                                            customer_key: invoice.CustomerRef,
                                            customer_name: this.state.selectedCustomer,
                                            files: accepted,
                                        })
                                    }}
                                >
                                    <h3>+</h3>
                                </StyledDropzone>
                            </div>
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
    uploadFile,
  }
)(Main);
