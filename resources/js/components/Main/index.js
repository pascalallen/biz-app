import React from 'react';
import {connect} from 'react-redux';

const mapStateToProps = (state) => ({
});
class Main extends React.Component {
  constructor (props) {
    super(props);
    this.state = {
    };
  }

  // getMonthly(){
  //   this.props.fetchMonthly(`/api/v1/propertyApi/${this.props.panelObject.component}/monthly`);
  // }

  // componentDidMount() {
  //   this.getAll();
  //   this.getMonthly();
  // }
  render () {
    return (
      <div>Main component</div>
    )
  }
}

export default connect(
  mapStateToProps,
  {
  }
)(Main);
