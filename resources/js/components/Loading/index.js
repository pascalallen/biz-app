import React from 'react';
import { CenterRow } from './styles';

class Loading extends React.Component { 
  render () {
    return (
      <CenterRow>
        <img src="/images/ajax-loading-transparent.gif" />
      </CenterRow>
    )
  }
}

export default Loading;