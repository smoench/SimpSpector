import React from "react";
import Issue from "./Issue";

export default class IssueGroup extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      open: false
    }
  }

  toggle() {
    this.setState({
      open: !this.state.open
    });
  }

  render() {
    return (
      <div>
        <div className="title" onClick={() => this.toggle()}>
          {this.renderBar()}
          <i className="dropdown icon"/>
          <i className="file icon"/>
          {this.props.file}
        </div>
        <div className={`content ${this.state.open ? 'active' : ''}`}>
          {this.state.open ? this.props.issues.map(issue => {
            let key = issue.file + issue.line + issue.level + issue.gadget + issue.title;
            return <Issue key={key} issue={issue}/>
          }) : null }
        </div>
      </div>
    );
  }

  renderBar() {
    return (
      <div style={{float: "right"}}>
                        <span className="ui" title="notice">
                <i className="blue info icon"/>
                2
            </span>
        <span className="ui" title="warning">
                <i className="yellow warning icon"/>
                1
            </span>
        <span className="ui" title="error">
                <i className="orange bug icon"/>
                1
            </span>

      </div>
    );
  }
}
