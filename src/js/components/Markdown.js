import React from "react";

export default class Markdown extends React.Component {
  render() {
    return (
      <div>
        {this.props.children}
      </div>
    );
  }
}
