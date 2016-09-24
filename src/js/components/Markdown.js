import React from "react";

import 'prismjs/components/prism-core';
import 'prismjs/components/prism-markup';
import 'prismjs/components/prism-twig';
import 'prismjs/components/prism-clike';
import 'prismjs/components/prism-php';
import 'prismjs/plugins/line-highlight/prism-line-highlight';
import 'prismjs/plugins/line-numbers/prism-line-numbers';

import 'prismjs/themes/prism.css';
import 'prismjs/plugins/line-highlight/prism-line-highlight.css';
import 'prismjs/plugins/line-numbers/prism-line-numbers.css';

export default class Markdown extends React.Component {
  componentDidMount() {
    Prism.highlightAll();
  }

  render() {
    return (
      <div ref={el => this.el = el} dangerouslySetInnerHTML={{__html: this.props.children}} />
    );
  }
}
