//
// Definitions for schema: tns
//  http://localhost/api/securityservice?wsdl#types1
//
//
// Constructor for XML Schema item {tns}loginRequestType
//
function tns_loginRequestType () {
    this.typeMarker = 'tns_loginRequestType';
    this._username = '';
    this._userpass = '';
    this._project = '';
}

//
// accessor is tns_loginRequestType.prototype.getUsername
// element get for username
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for username
// setter function is is tns_loginRequestType.prototype.setUsername
//
function tns_loginRequestType_getUsername() { return this._username;}

tns_loginRequestType.prototype.getUsername = tns_loginRequestType_getUsername;

function tns_loginRequestType_setUsername(value) { this._username = value;}

tns_loginRequestType.prototype.setUsername = tns_loginRequestType_setUsername;
//
// accessor is tns_loginRequestType.prototype.getUserpass
// element get for userpass
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for userpass
// setter function is is tns_loginRequestType.prototype.setUserpass
//
function tns_loginRequestType_getUserpass() { return this._userpass;}

tns_loginRequestType.prototype.getUserpass = tns_loginRequestType_getUserpass;

function tns_loginRequestType_setUserpass(value) { this._userpass = value;}

tns_loginRequestType.prototype.setUserpass = tns_loginRequestType_setUserpass;
//
// accessor is tns_loginRequestType.prototype.getProject
// element get for project
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for project
// setter function is is tns_loginRequestType.prototype.setProject
//
function tns_loginRequestType_getProject() { return this._project;}

tns_loginRequestType.prototype.getProject = tns_loginRequestType_getProject;

function tns_loginRequestType_setProject(value) { this._project = value;}

tns_loginRequestType.prototype.setProject = tns_loginRequestType_setProject;
//
// Serialize {tns}loginRequestType
//
function tns_loginRequestType_serialize(cxfjsutils, elementName, extraNamespaces) {
    var xml = '';
    if (elementName != null) {
     xml = xml + '<';
     xml = xml + elementName;
     xml = xml + ' ';
     xml = xml + 'xmlns:jns0=\'tns\' ';
     if (extraNamespaces) {
      xml = xml + ' ' + extraNamespaces;
     }
     xml = xml + '>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:username>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._username);
     xml = xml + '</jns0:username>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:userpass>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._userpass);
     xml = xml + '</jns0:userpass>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:project>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._project);
     xml = xml + '</jns0:project>';
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_loginRequestType.prototype.serialize = tns_loginRequestType_serialize;

function tns_loginRequestType_deserialize (cxfjsutils, element) {
    var newobject = new tns_loginRequestType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing username');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setUsername(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing userpass');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setUserpass(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing project');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setProject(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}loginResponseType
//
function tns_loginResponseType () {
    this.typeMarker = 'tns_loginResponseType';
    this._return = null;
}

//
// accessor is tns_loginResponseType.prototype.getReturn
// element get for return
// - element type is {tns}Token
// - required element
//
// element set for return
// setter function is is tns_loginResponseType.prototype.setReturn
//
function tns_loginResponseType_getReturn() { return this._return;}

tns_loginResponseType.prototype.getReturn = tns_loginResponseType_getReturn;

function tns_loginResponseType_setReturn(value) { this._return = value;}

tns_loginResponseType.prototype.setReturn = tns_loginResponseType_setReturn;
//
// Serialize {tns}loginResponseType
//
function tns_loginResponseType_serialize(cxfjsutils, elementName, extraNamespaces) {
    var xml = '';
    if (elementName != null) {
     xml = xml + '<';
     xml = xml + elementName;
     xml = xml + ' ';
     xml = xml + 'xmlns:jns0=\'tns\' ';
     if (extraNamespaces) {
      xml = xml + ' ' + extraNamespaces;
     }
     xml = xml + '>';
    }
    // block for local variables
    {
     xml = xml + this._return.serialize(cxfjsutils, 'jns0:return', null);
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_loginResponseType.prototype.serialize = tns_loginResponseType_serialize;

function tns_loginResponseType_deserialize (cxfjsutils, element) {
    var newobject = new tns_loginResponseType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing return');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_Token_deserialize(cxfjsutils, curElement);
    }
    newobject.setReturn(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}Token
//
function tns_Token () {
    this.typeMarker = 'tns_Token';
    this._Key = '';
    this._Url = '';
}

//
// accessor is tns_Token.prototype.getKey
// element get for Key
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Key
// setter function is is tns_Token.prototype.setKey
//
function tns_Token_getKey() { return this._Key;}

tns_Token.prototype.getKey = tns_Token_getKey;

function tns_Token_setKey(value) { this._Key = value;}

tns_Token.prototype.setKey = tns_Token_setKey;
//
// accessor is tns_Token.prototype.getUrl
// element get for Url
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Url
// setter function is is tns_Token.prototype.setUrl
//
function tns_Token_getUrl() { return this._Url;}

tns_Token.prototype.getUrl = tns_Token_getUrl;

function tns_Token_setUrl(value) { this._Url = value;}

tns_Token.prototype.setUrl = tns_Token_setUrl;
//
// Serialize {tns}Token
//
function tns_Token_serialize(cxfjsutils, elementName, extraNamespaces) {
    var xml = '';
    if (elementName != null) {
     xml = xml + '<';
     xml = xml + elementName;
     xml = xml + ' ';
     xml = xml + 'xmlns:jns0=\'tns\' ';
     if (extraNamespaces) {
      xml = xml + ' ' + extraNamespaces;
     }
     xml = xml + '>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Key>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Key);
     xml = xml + '</jns0:Key>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Url>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Url);
     xml = xml + '</jns0:Url>';
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_Token.prototype.serialize = tns_Token_serialize;

function tns_Token_deserialize (cxfjsutils, element) {
    var newobject = new tns_Token();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Key');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setKey(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Url');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setUrl(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Definitions for service: {tns}SecurityService
//

// Javascript for {tns}SecurityServicePortType

function tns_SecurityServicePortType () {
    this.jsutils = new CxfApacheOrgUtil();
    this.jsutils.interfaceObject = this;
    this.synchronous = false;
    this.url = null;
    this.client = null;
    this.response = null;
    this.globalElementSerializers = [];
    this.globalElementDeserializers = [];
    this.globalElementSerializers['{tns}loginResponseType'] = tns_loginResponseType_serialize;
    this.globalElementDeserializers['{tns}loginResponseType'] = tns_loginResponseType_deserialize;
    this.globalElementSerializers['{tns}loginRequestType'] = tns_loginRequestType_serialize;
    this.globalElementDeserializers['{tns}loginRequestType'] = tns_loginRequestType_deserialize;
    this.globalElementSerializers['{tns}loginRequestType'] = tns_loginRequestType_serialize;
    this.globalElementDeserializers['{tns}loginRequestType'] = tns_loginRequestType_deserialize;
    this.globalElementSerializers['{tns}loginResponseType'] = tns_loginResponseType_serialize;
    this.globalElementDeserializers['{tns}loginResponseType'] = tns_loginResponseType_deserialize;
    this.globalElementSerializers['{tns}Token'] = tns_Token_serialize;
    this.globalElementDeserializers['{tns}Token'] = tns_Token_deserialize;
}

function tns_login_op_onsuccess(client, responseXml) {
    if (client.user_onsuccess) {
     var responseObject = null;
     var element = responseXml.documentElement;
     this.jsutils.trace('responseXml: ' + this.jsutils.traceElementName(element));
     element = this.jsutils.getFirstElementChild(element);
     this.jsutils.trace('first element child: ' + this.jsutils.traceElementName(element));
     while (!this.jsutils.isNodeNamedNS(element, 'http://schemas.xmlsoap.org/soap/envelope/', 'Body')) {
      element = this.jsutils.getNextElementSibling(element);
      if (element == null) {
       throw 'No env:Body in message.'
      }
     }
     element = this.jsutils.getFirstElementChild(element);
     this.jsutils.trace('part element: ' + this.jsutils.traceElementName(element));
     this.jsutils.trace('calling tns_loginResponse_deserializeResponse');
     responseObject = tns_loginResponse_deserializeResponse(this.jsutils, element);
     client.user_onsuccess(responseObject);
    }
}

tns_SecurityServicePortType.prototype.login_onsuccess = tns_login_op_onsuccess;

function tns_login_op_onerror(client) {
    if (client.user_onerror) {
     var httpStatus;
     var httpStatusText;
     try {
      httpStatus = client.req.status;
      httpStatusText = client.req.statusText;
     } catch(e) {
      httpStatus = -1;
      httpStatusText = 'Error opening connection to server';
     }
     client.user_onerror(httpStatus, httpStatusText);
    }
}

tns_SecurityServicePortType.prototype.login_onerror = tns_login_op_onerror;

//
// Operation {tns}login
// Wrapped operation.
// parameter username
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter userpass
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter project
// - simple type {http://www.w3.org/2001/XMLSchema}string//
function tns_login_op(successCallback, errorCallback, username, userpass, project) {
    this.client = new CxfApacheOrgClient(this.jsutils);
    var xml = null;
    var args = new Array(3);
    args[0] = username;
    args[1] = userpass;
    args[2] = project;
    xml = this.loginRequest_serializeInput(this.jsutils, args);
    this.client.user_onsuccess = successCallback;
    this.client.user_onerror = errorCallback;
    var closureThis = this;
    this.client.onsuccess = function(client, responseXml) { closureThis.login_onsuccess(client, responseXml); };
    this.client.onerror = function(client) { closureThis.login_onerror(client); };
    var requestHeaders = [];
    requestHeaders['SOAPAction'] = 'tns.Login';
    this.jsutils.trace('synchronous = ' + this.synchronous);
    this.client.request(this.url, xml, null, this.synchronous, requestHeaders);
}

tns_SecurityServicePortType.prototype.login = tns_login_op;

function tns_loginRequest_serializeInput(cxfjsutils, args) {
    var wrapperObj = new tns_loginRequestType();
    wrapperObj.setUsername(args[0]);
    wrapperObj.setUserpass(args[1]);
    wrapperObj.setProject(args[2]);
    var xml;
    xml = cxfjsutils.beginSoap11Message("xmlns:jns0='tns' ");
    // block for local variables
    {
     xml = xml + wrapperObj.serialize(cxfjsutils, 'jns0:login', null);
    }
    xml = xml + cxfjsutils.endSoap11Message();
    return xml;
}

tns_SecurityServicePortType.prototype.loginRequest_serializeInput = tns_loginRequest_serializeInput;

function tns_loginResponse_deserializeResponse(cxfjsutils, partElement) {
    var returnObject = tns_loginResponseType_deserialize (cxfjsutils, partElement);

    return returnObject;
}
function tns_SecurityServicePortType_tns_SecurityServicePort () {
  this.url = 'http://Saturn/api/securityservice';
}
tns_SecurityServicePortType_tns_SecurityServicePort.prototype = new tns_SecurityServicePortType;
