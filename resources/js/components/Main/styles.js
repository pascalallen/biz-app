import styled from 'styled-components';
import { Row } from 'react-bootstrap';
import Dropzone from 'react-dropzone';

export const CenterRow = styled(Row)`
  display: flex;
  justify-content: center;
`;

export const TooltipDiv = styled.div`
  background-color: #f8f9fa;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 5px;
  list-style-type: none;
`;

export const FloatRight = styled.span`
  float: right;
  font-weight: bold;
  color: #28a745;
`;

export const StyledDropzone = styled(Dropzone)`
    margin-top: 10px;
`;
