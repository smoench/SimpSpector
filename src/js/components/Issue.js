import React from "react";
import PhpStormOpener from "./PhpStormOpener";
import Markdown from "./Markdown";
import {colorLevel, iconLevel} from "../helper";

export default class Issue extends React.Component {
  render() {
    return (
      <div>
        <div className={`ui top attached message ${colorLevel(this.props.issue)}`}>
          {this.renderLabel()}

          <h4 className="attached" style={{marginTop: 0}}> {/* use other css framework... */}
            <i className={`icon ${iconLevel(this.props.issue)}`} />
            {this.props.issue.gadget}
            {this.props.issue.level}
          </h4>

          <p>{this.props.issue.title}</p>
        </div>

        <div className="ui attached segment">
          <Markdown>{this.props.issue.description}</Markdown>
        </div>
      </div>
    );
  }

  renderLabel() {
    if (!this.props.issue.line || !this.props.issue.file) {
      return null;
    }

    return (
      <div className="ui top right attached label">
        <PhpStormOpener file={this.props.issue.file} line={this.props.issue.line}/>
        Line {this.props.issue.line}
      </div>
    );
  }
}
