import React from 'react';
import {connect} from 'react-redux';
import moment from 'moment';
import {fetchAll, fetchSingle} from '../../actions/invoiceActions';
import { FormGroup, FormControl, ControlLabel, Table } from 'react-bootstrap';
import { CenterRow, TooltipDiv, FloatRight } from './styles';
import Loading from '../Loading';

const mapStateToProps = (state) => ({
  invoice: state.invoice,
});

class Main extends React.Component {
  constructor (props) {
    super(props);
    this.state = {
      startDate: moment().subtract(1, 'days'),
      endDate: moment(),
    };

    this.getSingle = this.getSingle.bind(this);
  }

  getAll(){
    this.props.fetchAll(`/api/invoices`, {
      from: this.state.startDate.format("YYYY-MM-DD"),
      to: this.state.endDate.format("YYYY-MM-DD")
    });
  }

  getSingle(event){
    this.props.fetchSingle(`/api/invoices/${event.target.value}`, {
      from: this.state.startDate.format("YYYY-MM-DD"),
      to: this.state.endDate.format("YYYY-MM-DD"),
    });
  }

  componentDidMount() {
    this.getAll();
  }

  render () {
    return (
    <div className="container">
        <CenterRow>
            <h1>Invoices</h1>
            {this.props.invoice.fetchingAll ? <Loading />
            : <Table responsive condensed>
                <thead>
                    <tr>
                        <th>Doc No.</th>
                        <th>Balance</th>
                        <th>Transaction Date</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    {this.props.invoice.all && this.props.invoice.all.map((invoice,i) => (
                    <tr>
                        <td>{invoice.DocNumber}</td>
                        <td>{invoice.Balance}</td>
                        <td>{invoice.TxnDate}</td>
                        <td>{invoice.DueDate}</td>
                    </tr>
                    ))}
                </tbody>
            </Table>
            }
        </CenterRow>
    </div>
    )
  }
}

export default connect(
  mapStateToProps,
  {
    fetchAll,
    fetchSingle,
  }
)(Main);
