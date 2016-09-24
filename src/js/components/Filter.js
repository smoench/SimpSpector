import React from "react";

export default class Filter extends React.Component {
  render() {
    console.log(this.props.choices);

    return (
      <div style={{marginBottom: 25}}>
        <div className="ui vertical menu">
          <div className="item"><b>{this.props.title}</b></div>
          {this.props.choices.map(this.renderElement.bind(this))}
        </div>
      </div>
    );
  }

  filter(e, key) {
    e.preventDefault();
    this.props.filter(this.props.filterKey, key);
  }

  unfilter(e) {
    e.preventDefault();
    this.props.filter(this.props.filterKey, null);
  }

  renderElement(choice) {
    const clickHandler = choice.active ? this.unfilter.bind(this) : e => this.filter(e, choice.key);

    return (
      <a className={'item ' + (choice.active ? 'active teal' : '')} key={choice.key} onClick={clickHandler}>
        {choice.label}
        <div className={'ui label ' + (choice.active ? 'teal' : '')}>{choice.count}</div>
      </a>
    );
  }
}
