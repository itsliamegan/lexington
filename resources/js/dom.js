export default function q(dataJsName) {
  return document.querySelector(`[data-js=${dataJsName}]`) || undefined;
}

export function qAll(dataJsName) {
  return Array.from(document.querySelectorAll(`[data-js=${dataJsName}]`));
}

export function create(tagName, attributes) {
  const element = document.createElement(tagName);

  Object.entries(attributes).forEach(([name, value]) => {
    element.setAttribute(name, value);
  });

  return element;
}

export function removeChildren(dataJsName) {
  const elements = qAll(dataJsName);

  elements.forEach(element => {
    const children = Array.from(element.children);

    children.forEach(child => {
      child.remove();
    });
  });
}

export function exists(dataJsName) {
  return !!qAll(dataJsName).length;
}

export function changeAttribute(dataJsName, name, value) {
  const elements = qAll(dataJsName);

  elements.forEach(element => {
    element[name] = value;
  });
}

export function getAttribute(dataJsName, name) {
  const element = q(dataJsName);

  if (element) {
    return element.getAttribute(name) || undefined;
  }

  return undefined;
}

export function callMethod(dataJsName, methodName, ...args) {
  const elements = qAll(dataJsName);

  elements.forEach(element => {
    element[methodName](...args);
  });
}

export function listen(dataJsName, eventType, listener) {
  return document.addEventListener(eventType, event => {
    if (event.target.dataset.js === dataJsName) {
      listener(event);
    }
  });
}

export function fire(dataJsName, eventType) {
  const elements = qAll(dataJsName);
  const event = new Event(eventType, {
    bubbles: true,
    cancelable: true,
  });

  elements.forEach(element => {
    element.dispatchEvent(event);
  });
}
