/*! @source http://purl.eligrey.com/github/FileSaver.js/blob/master/FileSaver.js */
var saveAs = saveAs || "undefined" != typeof navigator && navigator.msSaveOrOpenBlob && navigator.msSaveOrOpenBlob.bind(navigator) || function (e) {
  "use strict";
  if ("undefined" == typeof navigator || !/MSIE [1-9]\./.test(navigator.userAgent)) {
    var t = e.document,
      n = function () {
        return e.URL || e.webkitURL || e
      },
      o = t.createElementNS("http://www.w3.org/1999/xhtml", "a"),
      r = "download" in o,
      i = function (n) {
        var o = t.createEvent("MouseEvents");
        o.initMouseEvent("click", !0, !1, e, 0, 0, 0, 0, 0, !1, !1, !1, !1, 0, null), n.dispatchEvent(o)
      },
      a = e.webkitRequestFileSystem,
      c = e.requestFileSystem || a || e.mozRequestFileSystem,
      s = function (t) {
        (e.setImmediate || e.setTimeout)(function () {
          throw t
        }, 0)
      },
      u = "application/octet-stream",
      f = 0,
      d = 500,
      l = function (t) {
        var o = function () {
          "string" == typeof t ? n().revokeObjectURL(t) : t.remove()
        };
        e.chrome ? o() : setTimeout(o, d)
      },
      v = function (e, t, n) {
        t = [].concat(t);
        for (var o = t.length; o--;) {
          var r = e["on" + t[o]];
          if ("function" == typeof r) try {
            r.call(e, n || e)
          } catch (i) {
            s(i)
          }
        }
      },
      p = function (t, s) {
        var d, p, w, y = this,
          m = t.type,
          S = !1,
          h = function () {
            v(y, "writestart progress write writeend".split(" "))
          },
          O = function () {
            if ((S || !d) && (d = n().createObjectURL(t)), p) p.location.href = d;
            else {
              var o = e.open(d, "_blank");
              void 0 == o && "undefined" != typeof safari && (e.location.href = d)
            }
            y.readyState = y.DONE, h(), l(d)
          },
          b = function (e) {
            return function () {
              return y.readyState !== y.DONE ? e.apply(this, arguments) : void 0
            }
          },
          g = {
            create: !0,
            exclusive: !1
          };
        return y.readyState = y.INIT, s || (s = "download"), r ? (d = n().createObjectURL(t), o.href = d, o.download = s, i(o), y.readyState = y.DONE, h(), void l(d)) : (/^\s*(?:text\/(?:plain|xml)|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(t.type) && (t = new Blob(["﻿", t], {
          type: t.type
        })), e.chrome && m && m !== u && (w = t.slice || t.webkitSlice, t = w.call(t, 0, t.size, u), S = !0), a && "download" !== s && (s += ".download"), (m === u || a) && (p = e), c ? (f += t.size, void c(e.TEMPORARY, f, b(function (e) {
          e.root.getDirectory("saved", g, b(function (e) {
            var n = function () {
              e.getFile(s, g, b(function (e) {
                e.createWriter(b(function (n) {
                  n.onwriteend = function (t) {
                    p.location.href = e.toURL(), y.readyState = y.DONE, v(y, "writeend", t), l(e)
                  }, n.onerror = function () {
                    var e = n.error;
                    e.code !== e.ABORT_ERR && O()
                  }, "writestart progress write abort".split(" ").forEach(function (e) {
                    n["on" + e] = y["on" + e]
                  }), n.write(t), y.abort = function () {
                    n.abort(), y.readyState = y.DONE
                  }, y.readyState = y.WRITING
                }), O)
              }), O)
            };
            e.getFile(s, {
              create: !1
            }, b(function (e) {
              e.remove(), n()
            }), b(function (e) {
              e.code === e.NOT_FOUND_ERR ? n() : O()
            }))
          }), O)
        }), O)) : void O())
      },
      w = p.prototype,
      y = function (e, t) {
        return new p(e, t)
      };
    return w.abort = function () {
      var e = this;
      e.readyState = e.DONE, v(e, "abort")
    }, w.readyState = w.INIT = 0, w.WRITING = 1, w.DONE = 2, w.error = w.onwritestart = w.onprogress = w.onwrite = w.onabort = w.onerror = w.onwriteend = null, y
  }
}("undefined" != typeof self && self || "undefined" != typeof window && window || this.content);
"undefined" != typeof module && module.exports ? module.exports.saveAs = saveAs : "undefined" != typeof define && null !== define && null != define.amd && define([], function () {
  return saveAs
});