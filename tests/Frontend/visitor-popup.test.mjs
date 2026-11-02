import test from 'node:test';
import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

const source = readFileSync(new URL('../../public/assets/js/main.js', import.meta.url), 'utf8');

function page({ imageReady = true, visible = true, granted = true, offline = false } = {}) {
  const documentEvents = {};
  const windowEvents = {};
  const imageEvents = {};
  const buttons = [{}, {}];
  const requests = [];
  let displays = 0;
  const picture = {
    complete: imageReady, naturalWidth: imageReady ? 800 : 0,
    addEventListener: (name, callback) => { imageEvents[name] = callback; },
  };
  const dialog = {
    open: false,
    dataset: { impressionUrl: '/informasi-modal/1/tampil' },
    querySelector: () => picture,
    querySelectorAll: () => buttons,
    addEventListener() {},
    showModal() { this.open = true; displays++; },
    close() { this.open = false; },
  };
  buttons.forEach(button => { button.addEventListener = (name, callback) => { button[name] = callback; }; });
  const document = {
    readyState: 'complete', visibilityState: visible ? 'visible' : 'hidden',
    getElementById: id => id === 'visitor-popup' ? dialog : null,
    querySelector: selector => selector === 'meta[name="csrf-token"]' ? { content: 'test-csrf' } : null,
    querySelectorAll: () => [],
    addEventListener: (name, callback) => { documentEvents[name] = callback; },
  };
  const window = { addEventListener: (name, callback) => { windowEvents[name] = callback; } };
  vm.runInNewContext(source, {
    document, window,
    fetch: async (url, options) => {
      requests.push({ url, options });
      if (offline) throw new Error('Network unavailable');
      return { ok: true, json: async () => ({ show: granted }) };
    },
  });
  return { document, documentEvents, windowEvents, imageEvents, picture, dialog, buttons, requests, displays: () => displays };
}

const settle = () => new Promise(resolve => setImmediate(resolve));

test('opens only after server permission and sends CSRF with the impression', async () => {
  const p = page();
  await settle();
  assert.equal(p.displays(), 1);
  assert.equal(p.requests[0].options.method, 'POST');
  assert.equal(p.requests[0].options.headers['X-CSRF-TOKEN'], 'test-csrf');
  p.documentEvents.visibilitychange();
  await settle();
  assert.equal(p.requests.length, 1);
  p.buttons[0].click();
  assert.equal(p.dialog.open, false);
});

test('does not display when another tab already claimed the popup', async () => {
  const p = page({ granted: false });
  await settle();
  assert.equal(p.displays(), 0);
});

test('waits for the image and does not count a failed image as displayed', async () => {
  const p = page({ imageReady: false });
  await settle();
  assert.equal(p.requests.length, 0);
  p.picture.complete = true;
  p.imageEvents.load();
  await settle();
  assert.equal(p.requests.length, 0);
  p.picture.naturalWidth = 800;
  p.imageEvents.load();
  await settle();
  assert.equal(p.displays(), 1);
});

test('defers the request until the tab becomes visible and closes before history restoration', async () => {
  const p = page({ visible: false });
  await settle();
  assert.equal(p.requests.length, 0);
  p.document.visibilityState = 'visible';
  p.documentEvents.visibilitychange();
  await settle();
  assert.equal(p.displays(), 1);
  p.windowEvents.pagehide();
  assert.equal(p.dialog.open, false);
});

test('keeps the popup closed when the request fails', async () => {
  const p = page({ offline: true });
  await settle();
  assert.equal(p.displays(), 0);
});
