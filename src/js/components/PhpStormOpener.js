import React from "react";

export default class PhpStormOpener extends React.Component {
  render() {
    return (
      <a className="phpstorm"
         href={`http://localhost:8091/?message=${this.props.file}:${this.props.line}`}>
        <i className="lightning inverted circular icon"/>
      </a>
    );
  }
}
