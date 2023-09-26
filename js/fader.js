/**
 * Fader
 * Author: Taigo Ito (https://qwel.design/)
 * Location: Fukui, Japan
 */

class Fader {

  constructor(elem) {
    // Faderの各要素
    this._elem = elem || document.querySelector('.fader');
    if (!this._elem) return;
    this._items = this._elem.children;
    if (!this._items.length || this._items.length <= 1) return;

    // 表示間隔
    this.interval = this._elem.dataset.interval || 5000;

    // 各状態管理
    this.currentIndex = 0; // 2枚目から操作
    this.itemsCount = this._items.length;

    // セットアップ
    //this._setupNavs();

    // 2番目以降の要素を背面に移動し、フェードさせておく
    for (let i = 1; i < this.itemsCount; i++) {
      this._items[i].style.zIndex--;
      this._items[i].classList.add('--fade');
    }

    // 開始
    this.startInterval();

  }


  // 再生
  startInterval() {
    this.isPlay = true;
    this._timeStart = null;
    this._loop(performance.now());

  }


  // 停止
  stopInterval() {
    this.isPlay = false;

  }


  _setupNavs() {
    // .fader__nav
    this._nav = document.createElement('ul');
    this._nav.classList.add('fader__nav');

    // .fader__navItem
    for (let i = 0; i < this.itemsCount; i++) {
      const li = document.createElement('li');
      li.classList.add('fader__navItem');
      li.dataset.targetIndex = i; // data-target-indexを挿入
      this._nav.appendChild(li);
    }

    // 現アイテムに.--currrentを付与
    this._navItems = this._nav.children;
    this._navItems = this._nav.children;
    this._navItems[this.currentIndex].classList.add('--current');

    this._elem.after(this._nav);

    this._handleEvents();

  }


  _handleEvents() {
    // ナビゲーション操作
    this._nav.addEventListener('click', (event) => {
      const target = event.target;
      if (target.dataset.targetIndex) {
        this.fade(target.dataset.targetIndex - 0); // 数値型へパース
        this.stopInterval();
      }
    });

  }


  _loop(timeCurrent) {
    if (!this._timeStart) {
      this._timeStart = timeCurrent;
    }
    const timeElapsed = timeCurrent - this._timeStart;

    timeElapsed < this.interval
      ? window.requestAnimationFrame(this._loop.bind(this))
      : this._done();

  }


  _done() {
    if (this.isPlay) {
      this.startInterval();
      this.fade((this.currentIndex + 1) % this.itemsCount);
    }

  }


  fade(index) {
    // prev(前面)とcurrent(背面)の要素をそれぞれ取得
    const prev = this._items[this.currentIndex];
    const current = this._items[index] || this._items[0];

    // 移動
    if (index > this.currentIndex) {
      // 正順の場合、前からindexまでの要素を前面へ移動
      for (let i = index; i > this.currentIndex; i--) this._items[i].style.zIndex++;
    } else {
      // 逆順の場合、後ろからindexまでの要素を背面へ移動
      for (let i = this.currentIndex; i > index; i--) this._items[i].style.zIndex--;
    }

    // アニメーション
    this._transitionEnd(current, () => {
      // フェードイン
      current.classList.remove('--fade');
    }).then(() => {
      // トランジションが終了したら、前の要素をフェードさせておく
      prev.classList.add('--fade');
    });

    // インデックスを継承
    this.currentIndex = index;

    // ナビゲーション
    if (this._nav.querySelector('.--current')) {
      this._nav.querySelector('.--current').classList.remove('--current');
    }
    this._navItems = this._nav.children;
    this._navItems[this.currentIndex].classList.add('--current');

  }


  _transitionEnd(elem, func) {
    let callback;
    const promise = new Promise((resolve, reject) => {
      callback = () => resolve(elem);
      elem.addEventListener('transitionend', callback);
    });
    func();
    promise.then((elem) => {
      elem.removeEventListener('transitionend', callback);
    });
    return promise;

  }

}
