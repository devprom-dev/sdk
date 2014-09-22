//
// Definitions for schema: tns
//  http://localhost/api/testservice?wsdl#types1
//
//
// Constructor for XML Schema item {tns}CreateRequestType
//
function tns_CreateRequestType () {
    this.typeMarker = 'tns_CreateRequestType';
    this._token = '';
    this._object = null;
}

//
// accessor is tns_CreateRequestType.prototype.getToken
// element get for token
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for token
// setter function is is tns_CreateRequestType.prototype.setToken
//
function tns_CreateRequestType_getToken() { return this._token;}

tns_CreateRequestType.prototype.getToken = tns_CreateRequestType_getToken;

function tns_CreateRequestType_setToken(value) { this._token = value;}

tns_CreateRequestType.prototype.setToken = tns_CreateRequestType_setToken;
//
// accessor is tns_CreateRequestType.prototype.getObject
// element get for object
// - element type is {tns}testscenario
// - required element
//
// element set for object
// setter function is is tns_CreateRequestType.prototype.setObject
//
function tns_CreateRequestType_getObject() { return this._object;}

tns_CreateRequestType.prototype.getObject = tns_CreateRequestType_getObject;

function tns_CreateRequestType_setObject(value) { this._object = value;}

tns_CreateRequestType.prototype.setObject = tns_CreateRequestType_setObject;
//
// Serialize {tns}CreateRequestType
//
function tns_CreateRequestType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:token>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._token);
     xml = xml + '</jns0:token>';
    }
    // block for local variables
    {
     xml = xml + this._object.serialize(cxfjsutils, 'jns0:object', null);
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_CreateRequestType.prototype.serialize = tns_CreateRequestType_serialize;

function tns_CreateRequestType_deserialize (cxfjsutils, element) {
    var newobject = new tns_CreateRequestType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing token');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setToken(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing object');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setObject(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}testexecutionresultArray
//
function tns_testexecutionresultArray () {
    this.typeMarker = 'tns_testexecutionresultArray';
}

//
// Serialize {tns}testexecutionresultArray
//
function tns_testexecutionresultArray_serialize(cxfjsutils, elementName, extraNamespaces) {
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
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_testexecutionresultArray.prototype.serialize = tns_testexecutionresultArray_serialize;

function tns_testexecutionresultArray_deserialize (cxfjsutils, element) {
    var newobject = new tns_testexecutionresultArray();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    return newobject;
}

//
// Constructor for XML Schema item {tns}AppendResponseType
//
function tns_AppendResponseType () {
    this.typeMarker = 'tns_AppendResponseType';
    this._return = null;
}

//
// accessor is tns_AppendResponseType.prototype.getReturn
// element get for return
// - element type is {tns}testscenario
// - required element
//
// element set for return
// setter function is is tns_AppendResponseType.prototype.setReturn
//
function tns_AppendResponseType_getReturn() { return this._return;}

tns_AppendResponseType.prototype.getReturn = tns_AppendResponseType_getReturn;

function tns_AppendResponseType_setReturn(value) { this._return = value;}

tns_AppendResponseType.prototype.setReturn = tns_AppendResponseType_setReturn;
//
// Serialize {tns}AppendResponseType
//
function tns_AppendResponseType_serialize(cxfjsutils, elementName, extraNamespaces) {
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

tns_AppendResponseType.prototype.serialize = tns_AppendResponseType_serialize;

function tns_AppendResponseType_deserialize (cxfjsutils, element) {
    var newobject = new tns_AppendResponseType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing return');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setReturn(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}RunRequestType
//
function tns_RunRequestType () {
    this.typeMarker = 'tns_RunRequestType';
    this._token = '';
    this._object = null;
    this._version = '';
    this._environment = null;
}

//
// accessor is tns_RunRequestType.prototype.getToken
// element get for token
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for token
// setter function is is tns_RunRequestType.prototype.setToken
//
function tns_RunRequestType_getToken() { return this._token;}

tns_RunRequestType.prototype.getToken = tns_RunRequestType_getToken;

function tns_RunRequestType_setToken(value) { this._token = value;}

tns_RunRequestType.prototype.setToken = tns_RunRequestType_setToken;
//
// accessor is tns_RunRequestType.prototype.getObject
// element get for object
// - element type is {tns}testscenario
// - required element
//
// element set for object
// setter function is is tns_RunRequestType.prototype.setObject
//
function tns_RunRequestType_getObject() { return this._object;}

tns_RunRequestType.prototype.getObject = tns_RunRequestType_getObject;

function tns_RunRequestType_setObject(value) { this._object = value;}

tns_RunRequestType.prototype.setObject = tns_RunRequestType_setObject;
//
// accessor is tns_RunRequestType.prototype.getVersion
// element get for version
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for version
// setter function is is tns_RunRequestType.prototype.setVersion
//
function tns_RunRequestType_getVersion() { return this._version;}

tns_RunRequestType.prototype.getVersion = tns_RunRequestType_getVersion;

function tns_RunRequestType_setVersion(value) { this._version = value;}

tns_RunRequestType.prototype.setVersion = tns_RunRequestType_setVersion;
//
// accessor is tns_RunRequestType.prototype.getEnvironment
// element get for environment
// - element type is {tns}environment
// - required element
//
// element set for environment
// setter function is is tns_RunRequestType.prototype.setEnvironment
//
function tns_RunRequestType_getEnvironment() { return this._environment;}

tns_RunRequestType.prototype.getEnvironment = tns_RunRequestType_getEnvironment;

function tns_RunRequestType_setEnvironment(value) { this._environment = value;}

tns_RunRequestType.prototype.setEnvironment = tns_RunRequestType_setEnvironment;
//
// Serialize {tns}RunRequestType
//
function tns_RunRequestType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:token>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._token);
     xml = xml + '</jns0:token>';
    }
    // block for local variables
    {
     xml = xml + this._object.serialize(cxfjsutils, 'jns0:object', null);
    }
    // block for local variables
    {
     xml = xml + '<jns0:version>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._version);
     xml = xml + '</jns0:version>';
    }
    // block for local variables
    {
     xml = xml + this._environment.serialize(cxfjsutils, 'jns0:environment', null);
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_RunRequestType.prototype.serialize = tns_RunRequestType_serialize;

function tns_RunRequestType_deserialize (cxfjsutils, element) {
    var newobject = new tns_RunRequestType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing token');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setToken(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing object');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setObject(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing version');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setVersion(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing environment');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_environment_deserialize(cxfjsutils, curElement);
    }
    newobject.setEnvironment(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}attachmentArray
//
function tns_attachmentArray () {
    this.typeMarker = 'tns_attachmentArray';
}

//
// Serialize {tns}attachmentArray
//
function tns_attachmentArray_serialize(cxfjsutils, elementName, extraNamespaces) {
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
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_attachmentArray.prototype.serialize = tns_attachmentArray_serialize;

function tns_attachmentArray_deserialize (cxfjsutils, element) {
    var newobject = new tns_attachmentArray();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    return newobject;
}

//
// Constructor for XML Schema item {tns}testscenarioArray
//
function tns_testscenarioArray () {
    this.typeMarker = 'tns_testscenarioArray';
}

//
// Serialize {tns}testscenarioArray
//
function tns_testscenarioArray_serialize(cxfjsutils, elementName, extraNamespaces) {
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
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_testscenarioArray.prototype.serialize = tns_testscenarioArray_serialize;

function tns_testscenarioArray_deserialize (cxfjsutils, element) {
    var newobject = new tns_testscenarioArray();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    return newobject;
}

//
// Constructor for XML Schema item {tns}environment
//
function tns_environment () {
    this.typeMarker = 'tns_environment';
    this._Caption = '';
    this._Description = '';
    this._RecordCreated = '';
    this._RecordModified = '';
    this._Id = 0;
    this._ClassName = '';
}

//
// accessor is tns_environment.prototype.getCaption
// element get for Caption
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Caption
// setter function is is tns_environment.prototype.setCaption
//
function tns_environment_getCaption() { return this._Caption;}

tns_environment.prototype.getCaption = tns_environment_getCaption;

function tns_environment_setCaption(value) { this._Caption = value;}

tns_environment.prototype.setCaption = tns_environment_setCaption;
//
// accessor is tns_environment.prototype.getDescription
// element get for Description
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Description
// setter function is is tns_environment.prototype.setDescription
//
function tns_environment_getDescription() { return this._Description;}

tns_environment.prototype.getDescription = tns_environment_getDescription;

function tns_environment_setDescription(value) { this._Description = value;}

tns_environment.prototype.setDescription = tns_environment_setDescription;
//
// accessor is tns_environment.prototype.getRecordCreated
// element get for RecordCreated
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordCreated
// setter function is is tns_environment.prototype.setRecordCreated
//
function tns_environment_getRecordCreated() { return this._RecordCreated;}

tns_environment.prototype.getRecordCreated = tns_environment_getRecordCreated;

function tns_environment_setRecordCreated(value) { this._RecordCreated = value;}

tns_environment.prototype.setRecordCreated = tns_environment_setRecordCreated;
//
// accessor is tns_environment.prototype.getRecordModified
// element get for RecordModified
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordModified
// setter function is is tns_environment.prototype.setRecordModified
//
function tns_environment_getRecordModified() { return this._RecordModified;}

tns_environment.prototype.getRecordModified = tns_environment_getRecordModified;

function tns_environment_setRecordModified(value) { this._RecordModified = value;}

tns_environment.prototype.setRecordModified = tns_environment_setRecordModified;
//
// accessor is tns_environment.prototype.getId
// element get for Id
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Id
// setter function is is tns_environment.prototype.setId
//
function tns_environment_getId() { return this._Id;}

tns_environment.prototype.getId = tns_environment_getId;

function tns_environment_setId(value) { this._Id = value;}

tns_environment.prototype.setId = tns_environment_setId;
//
// accessor is tns_environment.prototype.getClassName
// element get for ClassName
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ClassName
// setter function is is tns_environment.prototype.setClassName
//
function tns_environment_getClassName() { return this._ClassName;}

tns_environment.prototype.getClassName = tns_environment_getClassName;

function tns_environment_setClassName(value) { this._ClassName = value;}

tns_environment.prototype.setClassName = tns_environment_setClassName;
//
// Serialize {tns}environment
//
function tns_environment_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:Caption>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Caption);
     xml = xml + '</jns0:Caption>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Description>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Description);
     xml = xml + '</jns0:Description>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordCreated>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordCreated);
     xml = xml + '</jns0:RecordCreated>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordModified>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordModified);
     xml = xml + '</jns0:RecordModified>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Id>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Id);
     xml = xml + '</jns0:Id>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ClassName>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ClassName);
     xml = xml + '</jns0:ClassName>';
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_environment.prototype.serialize = tns_environment_serialize;

function tns_environment_deserialize (cxfjsutils, element) {
    var newobject = new tns_environment();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Caption');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setCaption(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Description');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setDescription(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordCreated');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordCreated(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordModified');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordModified(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Id');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setId(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ClassName');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setClassName(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}FindResponseType
//
function tns_FindResponseType () {
    this.typeMarker = 'tns_FindResponseType';
    this._return = null;
}

//
// accessor is tns_FindResponseType.prototype.getReturn
// element get for return
// - element type is {tns}testscenario
// - required element
//
// element set for return
// setter function is is tns_FindResponseType.prototype.setReturn
//
function tns_FindResponseType_getReturn() { return this._return;}

tns_FindResponseType.prototype.getReturn = tns_FindResponseType_getReturn;

function tns_FindResponseType_setReturn(value) { this._return = value;}

tns_FindResponseType.prototype.setReturn = tns_FindResponseType_setReturn;
//
// Serialize {tns}FindResponseType
//
function tns_FindResponseType_serialize(cxfjsutils, elementName, extraNamespaces) {
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

tns_FindResponseType.prototype.serialize = tns_FindResponseType_serialize;

function tns_FindResponseType_deserialize (cxfjsutils, element) {
    var newobject = new tns_FindResponseType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing return');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setReturn(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}testexecutionresult
//
function tns_testexecutionresult () {
    this.typeMarker = 'tns_testexecutionresult';
    this._Caption = '';
    this._ReferenceName = '';
    this._RecordCreated = '';
    this._RecordModified = '';
    this._Id = 0;
    this._ClassName = '';
}

//
// accessor is tns_testexecutionresult.prototype.getCaption
// element get for Caption
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Caption
// setter function is is tns_testexecutionresult.prototype.setCaption
//
function tns_testexecutionresult_getCaption() { return this._Caption;}

tns_testexecutionresult.prototype.getCaption = tns_testexecutionresult_getCaption;

function tns_testexecutionresult_setCaption(value) { this._Caption = value;}

tns_testexecutionresult.prototype.setCaption = tns_testexecutionresult_setCaption;
//
// accessor is tns_testexecutionresult.prototype.getReferenceName
// element get for ReferenceName
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ReferenceName
// setter function is is tns_testexecutionresult.prototype.setReferenceName
//
function tns_testexecutionresult_getReferenceName() { return this._ReferenceName;}

tns_testexecutionresult.prototype.getReferenceName = tns_testexecutionresult_getReferenceName;

function tns_testexecutionresult_setReferenceName(value) { this._ReferenceName = value;}

tns_testexecutionresult.prototype.setReferenceName = tns_testexecutionresult_setReferenceName;
//
// accessor is tns_testexecutionresult.prototype.getRecordCreated
// element get for RecordCreated
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordCreated
// setter function is is tns_testexecutionresult.prototype.setRecordCreated
//
function tns_testexecutionresult_getRecordCreated() { return this._RecordCreated;}

tns_testexecutionresult.prototype.getRecordCreated = tns_testexecutionresult_getRecordCreated;

function tns_testexecutionresult_setRecordCreated(value) { this._RecordCreated = value;}

tns_testexecutionresult.prototype.setRecordCreated = tns_testexecutionresult_setRecordCreated;
//
// accessor is tns_testexecutionresult.prototype.getRecordModified
// element get for RecordModified
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordModified
// setter function is is tns_testexecutionresult.prototype.setRecordModified
//
function tns_testexecutionresult_getRecordModified() { return this._RecordModified;}

tns_testexecutionresult.prototype.getRecordModified = tns_testexecutionresult_getRecordModified;

function tns_testexecutionresult_setRecordModified(value) { this._RecordModified = value;}

tns_testexecutionresult.prototype.setRecordModified = tns_testexecutionresult_setRecordModified;
//
// accessor is tns_testexecutionresult.prototype.getId
// element get for Id
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Id
// setter function is is tns_testexecutionresult.prototype.setId
//
function tns_testexecutionresult_getId() { return this._Id;}

tns_testexecutionresult.prototype.getId = tns_testexecutionresult_getId;

function tns_testexecutionresult_setId(value) { this._Id = value;}

tns_testexecutionresult.prototype.setId = tns_testexecutionresult_setId;
//
// accessor is tns_testexecutionresult.prototype.getClassName
// element get for ClassName
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ClassName
// setter function is is tns_testexecutionresult.prototype.setClassName
//
function tns_testexecutionresult_getClassName() { return this._ClassName;}

tns_testexecutionresult.prototype.getClassName = tns_testexecutionresult_getClassName;

function tns_testexecutionresult_setClassName(value) { this._ClassName = value;}

tns_testexecutionresult.prototype.setClassName = tns_testexecutionresult_setClassName;
//
// Serialize {tns}testexecutionresult
//
function tns_testexecutionresult_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:Caption>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Caption);
     xml = xml + '</jns0:Caption>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ReferenceName>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ReferenceName);
     xml = xml + '</jns0:ReferenceName>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordCreated>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordCreated);
     xml = xml + '</jns0:RecordCreated>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordModified>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordModified);
     xml = xml + '</jns0:RecordModified>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Id>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Id);
     xml = xml + '</jns0:Id>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ClassName>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ClassName);
     xml = xml + '</jns0:ClassName>';
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_testexecutionresult.prototype.serialize = tns_testexecutionresult_serialize;

function tns_testexecutionresult_deserialize (cxfjsutils, element) {
    var newobject = new tns_testexecutionresult();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Caption');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setCaption(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ReferenceName');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setReferenceName(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordCreated');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordCreated(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordModified');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordModified(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Id');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setId(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ClassName');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setClassName(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}CreateResponseType
//
function tns_CreateResponseType () {
    this.typeMarker = 'tns_CreateResponseType';
    this._return = null;
}

//
// accessor is tns_CreateResponseType.prototype.getReturn
// element get for return
// - element type is {tns}testscenario
// - required element
//
// element set for return
// setter function is is tns_CreateResponseType.prototype.setReturn
//
function tns_CreateResponseType_getReturn() { return this._return;}

tns_CreateResponseType.prototype.getReturn = tns_CreateResponseType_getReturn;

function tns_CreateResponseType_setReturn(value) { this._return = value;}

tns_CreateResponseType.prototype.setReturn = tns_CreateResponseType_setReturn;
//
// Serialize {tns}CreateResponseType
//
function tns_CreateResponseType_serialize(cxfjsutils, elementName, extraNamespaces) {
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

tns_CreateResponseType.prototype.serialize = tns_CreateResponseType_serialize;

function tns_CreateResponseType_deserialize (cxfjsutils, element) {
    var newobject = new tns_CreateResponseType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing return');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setReturn(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}ReportIssueRequestType
//
function tns_ReportIssueRequestType () {
    this.typeMarker = 'tns_ReportIssueRequestType';
    this._token = '';
    this._execution = null;
    this._test = null;
    this._issue = null;
}

//
// accessor is tns_ReportIssueRequestType.prototype.getToken
// element get for token
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for token
// setter function is is tns_ReportIssueRequestType.prototype.setToken
//
function tns_ReportIssueRequestType_getToken() { return this._token;}

tns_ReportIssueRequestType.prototype.getToken = tns_ReportIssueRequestType_getToken;

function tns_ReportIssueRequestType_setToken(value) { this._token = value;}

tns_ReportIssueRequestType.prototype.setToken = tns_ReportIssueRequestType_setToken;
//
// accessor is tns_ReportIssueRequestType.prototype.getExecution
// element get for execution
// - element type is {tns}testexecution
// - required element
//
// element set for execution
// setter function is is tns_ReportIssueRequestType.prototype.setExecution
//
function tns_ReportIssueRequestType_getExecution() { return this._execution;}

tns_ReportIssueRequestType.prototype.getExecution = tns_ReportIssueRequestType_getExecution;

function tns_ReportIssueRequestType_setExecution(value) { this._execution = value;}

tns_ReportIssueRequestType.prototype.setExecution = tns_ReportIssueRequestType_setExecution;
//
// accessor is tns_ReportIssueRequestType.prototype.getTest
// element get for test
// - element type is {tns}testscenario
// - required element
//
// element set for test
// setter function is is tns_ReportIssueRequestType.prototype.setTest
//
function tns_ReportIssueRequestType_getTest() { return this._test;}

tns_ReportIssueRequestType.prototype.getTest = tns_ReportIssueRequestType_getTest;

function tns_ReportIssueRequestType_setTest(value) { this._test = value;}

tns_ReportIssueRequestType.prototype.setTest = tns_ReportIssueRequestType_setTest;
//
// accessor is tns_ReportIssueRequestType.prototype.getIssue
// element get for issue
// - element type is {tns}request
// - required element
//
// element set for issue
// setter function is is tns_ReportIssueRequestType.prototype.setIssue
//
function tns_ReportIssueRequestType_getIssue() { return this._issue;}

tns_ReportIssueRequestType.prototype.getIssue = tns_ReportIssueRequestType_getIssue;

function tns_ReportIssueRequestType_setIssue(value) { this._issue = value;}

tns_ReportIssueRequestType.prototype.setIssue = tns_ReportIssueRequestType_setIssue;
//
// Serialize {tns}ReportIssueRequestType
//
function tns_ReportIssueRequestType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:token>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._token);
     xml = xml + '</jns0:token>';
    }
    // block for local variables
    {
     xml = xml + this._execution.serialize(cxfjsutils, 'jns0:execution', null);
    }
    // block for local variables
    {
     xml = xml + this._test.serialize(cxfjsutils, 'jns0:test', null);
    }
    // block for local variables
    {
     xml = xml + this._issue.serialize(cxfjsutils, 'jns0:issue', null);
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_ReportIssueRequestType.prototype.serialize = tns_ReportIssueRequestType_serialize;

function tns_ReportIssueRequestType_deserialize (cxfjsutils, element) {
    var newobject = new tns_ReportIssueRequestType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing token');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setToken(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing execution');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testexecution_deserialize(cxfjsutils, curElement);
    }
    newobject.setExecution(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing test');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setTest(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing issue');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_request_deserialize(cxfjsutils, curElement);
    }
    newobject.setIssue(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}ReportIssueResponseType
//
function tns_ReportIssueResponseType () {
    this.typeMarker = 'tns_ReportIssueResponseType';
    this._return = null;
}

//
// accessor is tns_ReportIssueResponseType.prototype.getReturn
// element get for return
// - element type is {tns}request
// - required element
//
// element set for return
// setter function is is tns_ReportIssueResponseType.prototype.setReturn
//
function tns_ReportIssueResponseType_getReturn() { return this._return;}

tns_ReportIssueResponseType.prototype.getReturn = tns_ReportIssueResponseType_getReturn;

function tns_ReportIssueResponseType_setReturn(value) { this._return = value;}

tns_ReportIssueResponseType.prototype.setReturn = tns_ReportIssueResponseType_setReturn;
//
// Serialize {tns}ReportIssueResponseType
//
function tns_ReportIssueResponseType_serialize(cxfjsutils, elementName, extraNamespaces) {
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

tns_ReportIssueResponseType.prototype.serialize = tns_ReportIssueResponseType_serialize;

function tns_ReportIssueResponseType_deserialize (cxfjsutils, element) {
    var newobject = new tns_ReportIssueResponseType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing return');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_request_deserialize(cxfjsutils, curElement);
    }
    newobject.setReturn(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}ReportFileResponseType
//
function tns_ReportFileResponseType () {
    this.typeMarker = 'tns_ReportFileResponseType';
}

//
// Serialize {tns}ReportFileResponseType
//
function tns_ReportFileResponseType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_ReportFileResponseType.prototype.serialize = tns_ReportFileResponseType_serialize;

function tns_ReportFileResponseType_deserialize (cxfjsutils, element) {
    var newobject = new tns_ReportFileResponseType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    return newobject;
}

//
// Constructor for XML Schema item {tns}GetResultResponseType
//
function tns_GetResultResponseType () {
    this.typeMarker = 'tns_GetResultResponseType';
    this._return = null;
}

//
// accessor is tns_GetResultResponseType.prototype.getReturn
// element get for return
// - element type is {tns}testexecutionresult
// - required element
//
// element set for return
// setter function is is tns_GetResultResponseType.prototype.setReturn
//
function tns_GetResultResponseType_getReturn() { return this._return;}

tns_GetResultResponseType.prototype.getReturn = tns_GetResultResponseType_getReturn;

function tns_GetResultResponseType_setReturn(value) { this._return = value;}

tns_GetResultResponseType.prototype.setReturn = tns_GetResultResponseType_setReturn;
//
// Serialize {tns}GetResultResponseType
//
function tns_GetResultResponseType_serialize(cxfjsutils, elementName, extraNamespaces) {
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

tns_GetResultResponseType.prototype.serialize = tns_GetResultResponseType_serialize;

function tns_GetResultResponseType_deserialize (cxfjsutils, element) {
    var newobject = new tns_GetResultResponseType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing return');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testexecutionresult_deserialize(cxfjsutils, curElement);
    }
    newobject.setReturn(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}testscenario
//
function tns_testscenario () {
    this.typeMarker = 'tns_testscenario';
    this._Caption = '';
    this._ReferenceName = '';
    this._Content = '';
    this._ParentPage = 0;
    this._Project = 0;
    this._Author = 0;
    this._UserField1 = '';
    this._IsTemplate = '';
    this._UserField2 = '';
    this._IsArchived = '';
    this._UserField3 = '';
    this._IsDraft = '';
    this._ContentEditor = '';
    this._State = '';
    this._PageType = 0;
    this._RecordCreated = '';
    this._RecordModified = '';
    this._Id = 0;
    this._ClassName = '';
}

//
// accessor is tns_testscenario.prototype.getCaption
// element get for Caption
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Caption
// setter function is is tns_testscenario.prototype.setCaption
//
function tns_testscenario_getCaption() { return this._Caption;}

tns_testscenario.prototype.getCaption = tns_testscenario_getCaption;

function tns_testscenario_setCaption(value) { this._Caption = value;}

tns_testscenario.prototype.setCaption = tns_testscenario_setCaption;
//
// accessor is tns_testscenario.prototype.getReferenceName
// element get for ReferenceName
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ReferenceName
// setter function is is tns_testscenario.prototype.setReferenceName
//
function tns_testscenario_getReferenceName() { return this._ReferenceName;}

tns_testscenario.prototype.getReferenceName = tns_testscenario_getReferenceName;

function tns_testscenario_setReferenceName(value) { this._ReferenceName = value;}

tns_testscenario.prototype.setReferenceName = tns_testscenario_setReferenceName;
//
// accessor is tns_testscenario.prototype.getContent
// element get for Content
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Content
// setter function is is tns_testscenario.prototype.setContent
//
function tns_testscenario_getContent() { return this._Content;}

tns_testscenario.prototype.getContent = tns_testscenario_getContent;

function tns_testscenario_setContent(value) { this._Content = value;}

tns_testscenario.prototype.setContent = tns_testscenario_setContent;
//
// accessor is tns_testscenario.prototype.getParentPage
// element get for ParentPage
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for ParentPage
// setter function is is tns_testscenario.prototype.setParentPage
//
function tns_testscenario_getParentPage() { return this._ParentPage;}

tns_testscenario.prototype.getParentPage = tns_testscenario_getParentPage;

function tns_testscenario_setParentPage(value) { this._ParentPage = value;}

tns_testscenario.prototype.setParentPage = tns_testscenario_setParentPage;
//
// accessor is tns_testscenario.prototype.getProject
// element get for Project
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Project
// setter function is is tns_testscenario.prototype.setProject
//
function tns_testscenario_getProject() { return this._Project;}

tns_testscenario.prototype.getProject = tns_testscenario_getProject;

function tns_testscenario_setProject(value) { this._Project = value;}

tns_testscenario.prototype.setProject = tns_testscenario_setProject;
//
// accessor is tns_testscenario.prototype.getAuthor
// element get for Author
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Author
// setter function is is tns_testscenario.prototype.setAuthor
//
function tns_testscenario_getAuthor() { return this._Author;}

tns_testscenario.prototype.getAuthor = tns_testscenario_getAuthor;

function tns_testscenario_setAuthor(value) { this._Author = value;}

tns_testscenario.prototype.setAuthor = tns_testscenario_setAuthor;
//
// accessor is tns_testscenario.prototype.getUserField1
// element get for UserField1
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for UserField1
// setter function is is tns_testscenario.prototype.setUserField1
//
function tns_testscenario_getUserField1() { return this._UserField1;}

tns_testscenario.prototype.getUserField1 = tns_testscenario_getUserField1;

function tns_testscenario_setUserField1(value) { this._UserField1 = value;}

tns_testscenario.prototype.setUserField1 = tns_testscenario_setUserField1;
//
// accessor is tns_testscenario.prototype.getIsTemplate
// element get for IsTemplate
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for IsTemplate
// setter function is is tns_testscenario.prototype.setIsTemplate
//
function tns_testscenario_getIsTemplate() { return this._IsTemplate;}

tns_testscenario.prototype.getIsTemplate = tns_testscenario_getIsTemplate;

function tns_testscenario_setIsTemplate(value) { this._IsTemplate = value;}

tns_testscenario.prototype.setIsTemplate = tns_testscenario_setIsTemplate;
//
// accessor is tns_testscenario.prototype.getUserField2
// element get for UserField2
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for UserField2
// setter function is is tns_testscenario.prototype.setUserField2
//
function tns_testscenario_getUserField2() { return this._UserField2;}

tns_testscenario.prototype.getUserField2 = tns_testscenario_getUserField2;

function tns_testscenario_setUserField2(value) { this._UserField2 = value;}

tns_testscenario.prototype.setUserField2 = tns_testscenario_setUserField2;
//
// accessor is tns_testscenario.prototype.getIsArchived
// element get for IsArchived
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for IsArchived
// setter function is is tns_testscenario.prototype.setIsArchived
//
function tns_testscenario_getIsArchived() { return this._IsArchived;}

tns_testscenario.prototype.getIsArchived = tns_testscenario_getIsArchived;

function tns_testscenario_setIsArchived(value) { this._IsArchived = value;}

tns_testscenario.prototype.setIsArchived = tns_testscenario_setIsArchived;
//
// accessor is tns_testscenario.prototype.getUserField3
// element get for UserField3
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for UserField3
// setter function is is tns_testscenario.prototype.setUserField3
//
function tns_testscenario_getUserField3() { return this._UserField3;}

tns_testscenario.prototype.getUserField3 = tns_testscenario_getUserField3;

function tns_testscenario_setUserField3(value) { this._UserField3 = value;}

tns_testscenario.prototype.setUserField3 = tns_testscenario_setUserField3;
//
// accessor is tns_testscenario.prototype.getIsDraft
// element get for IsDraft
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for IsDraft
// setter function is is tns_testscenario.prototype.setIsDraft
//
function tns_testscenario_getIsDraft() { return this._IsDraft;}

tns_testscenario.prototype.getIsDraft = tns_testscenario_getIsDraft;

function tns_testscenario_setIsDraft(value) { this._IsDraft = value;}

tns_testscenario.prototype.setIsDraft = tns_testscenario_setIsDraft;
//
// accessor is tns_testscenario.prototype.getContentEditor
// element get for ContentEditor
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ContentEditor
// setter function is is tns_testscenario.prototype.setContentEditor
//
function tns_testscenario_getContentEditor() { return this._ContentEditor;}

tns_testscenario.prototype.getContentEditor = tns_testscenario_getContentEditor;

function tns_testscenario_setContentEditor(value) { this._ContentEditor = value;}

tns_testscenario.prototype.setContentEditor = tns_testscenario_setContentEditor;
//
// accessor is tns_testscenario.prototype.getState
// element get for State
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for State
// setter function is is tns_testscenario.prototype.setState
//
function tns_testscenario_getState() { return this._State;}

tns_testscenario.prototype.getState = tns_testscenario_getState;

function tns_testscenario_setState(value) { this._State = value;}

tns_testscenario.prototype.setState = tns_testscenario_setState;
//
// accessor is tns_testscenario.prototype.getPageType
// element get for PageType
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for PageType
// setter function is is tns_testscenario.prototype.setPageType
//
function tns_testscenario_getPageType() { return this._PageType;}

tns_testscenario.prototype.getPageType = tns_testscenario_getPageType;

function tns_testscenario_setPageType(value) { this._PageType = value;}

tns_testscenario.prototype.setPageType = tns_testscenario_setPageType;
//
// accessor is tns_testscenario.prototype.getRecordCreated
// element get for RecordCreated
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordCreated
// setter function is is tns_testscenario.prototype.setRecordCreated
//
function tns_testscenario_getRecordCreated() { return this._RecordCreated;}

tns_testscenario.prototype.getRecordCreated = tns_testscenario_getRecordCreated;

function tns_testscenario_setRecordCreated(value) { this._RecordCreated = value;}

tns_testscenario.prototype.setRecordCreated = tns_testscenario_setRecordCreated;
//
// accessor is tns_testscenario.prototype.getRecordModified
// element get for RecordModified
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordModified
// setter function is is tns_testscenario.prototype.setRecordModified
//
function tns_testscenario_getRecordModified() { return this._RecordModified;}

tns_testscenario.prototype.getRecordModified = tns_testscenario_getRecordModified;

function tns_testscenario_setRecordModified(value) { this._RecordModified = value;}

tns_testscenario.prototype.setRecordModified = tns_testscenario_setRecordModified;
//
// accessor is tns_testscenario.prototype.getId
// element get for Id
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Id
// setter function is is tns_testscenario.prototype.setId
//
function tns_testscenario_getId() { return this._Id;}

tns_testscenario.prototype.getId = tns_testscenario_getId;

function tns_testscenario_setId(value) { this._Id = value;}

tns_testscenario.prototype.setId = tns_testscenario_setId;
//
// accessor is tns_testscenario.prototype.getClassName
// element get for ClassName
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ClassName
// setter function is is tns_testscenario.prototype.setClassName
//
function tns_testscenario_getClassName() { return this._ClassName;}

tns_testscenario.prototype.getClassName = tns_testscenario_getClassName;

function tns_testscenario_setClassName(value) { this._ClassName = value;}

tns_testscenario.prototype.setClassName = tns_testscenario_setClassName;
//
// Serialize {tns}testscenario
//
function tns_testscenario_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:Caption>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Caption);
     xml = xml + '</jns0:Caption>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ReferenceName>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ReferenceName);
     xml = xml + '</jns0:ReferenceName>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Content>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Content);
     xml = xml + '</jns0:Content>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ParentPage>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ParentPage);
     xml = xml + '</jns0:ParentPage>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Project>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Project);
     xml = xml + '</jns0:Project>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Author>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Author);
     xml = xml + '</jns0:Author>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:UserField1>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._UserField1);
     xml = xml + '</jns0:UserField1>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:IsTemplate>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._IsTemplate);
     xml = xml + '</jns0:IsTemplate>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:UserField2>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._UserField2);
     xml = xml + '</jns0:UserField2>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:IsArchived>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._IsArchived);
     xml = xml + '</jns0:IsArchived>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:UserField3>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._UserField3);
     xml = xml + '</jns0:UserField3>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:IsDraft>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._IsDraft);
     xml = xml + '</jns0:IsDraft>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ContentEditor>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ContentEditor);
     xml = xml + '</jns0:ContentEditor>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:State>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._State);
     xml = xml + '</jns0:State>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:PageType>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._PageType);
     xml = xml + '</jns0:PageType>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordCreated>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordCreated);
     xml = xml + '</jns0:RecordCreated>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordModified>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordModified);
     xml = xml + '</jns0:RecordModified>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Id>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Id);
     xml = xml + '</jns0:Id>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ClassName>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ClassName);
     xml = xml + '</jns0:ClassName>';
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_testscenario.prototype.serialize = tns_testscenario_serialize;

function tns_testscenario_deserialize (cxfjsutils, element) {
    var newobject = new tns_testscenario();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Caption');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setCaption(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ReferenceName');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setReferenceName(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Content');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setContent(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ParentPage');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setParentPage(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Project');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setProject(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Author');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setAuthor(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing UserField1');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setUserField1(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing IsTemplate');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setIsTemplate(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing UserField2');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setUserField2(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing IsArchived');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setIsArchived(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing UserField3');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setUserField3(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing IsDraft');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setIsDraft(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ContentEditor');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setContentEditor(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing State');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setState(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing PageType');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setPageType(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordCreated');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordCreated(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordModified');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordModified(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Id');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setId(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ClassName');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setClassName(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}testexecution
//
function tns_testexecution () {
    this.typeMarker = 'tns_testexecution';
    this._TestScenario = 0;
    this._Environment = 0;
    this._Version = '';
    this._Result = 0;
    this._RecordCreated = '';
    this._RecordModified = '';
    this._Id = 0;
    this._ClassName = '';
}

//
// accessor is tns_testexecution.prototype.getTestScenario
// element get for TestScenario
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for TestScenario
// setter function is is tns_testexecution.prototype.setTestScenario
//
function tns_testexecution_getTestScenario() { return this._TestScenario;}

tns_testexecution.prototype.getTestScenario = tns_testexecution_getTestScenario;

function tns_testexecution_setTestScenario(value) { this._TestScenario = value;}

tns_testexecution.prototype.setTestScenario = tns_testexecution_setTestScenario;
//
// accessor is tns_testexecution.prototype.getEnvironment
// element get for Environment
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Environment
// setter function is is tns_testexecution.prototype.setEnvironment
//
function tns_testexecution_getEnvironment() { return this._Environment;}

tns_testexecution.prototype.getEnvironment = tns_testexecution_getEnvironment;

function tns_testexecution_setEnvironment(value) { this._Environment = value;}

tns_testexecution.prototype.setEnvironment = tns_testexecution_setEnvironment;
//
// accessor is tns_testexecution.prototype.getVersion
// element get for Version
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Version
// setter function is is tns_testexecution.prototype.setVersion
//
function tns_testexecution_getVersion() { return this._Version;}

tns_testexecution.prototype.getVersion = tns_testexecution_getVersion;

function tns_testexecution_setVersion(value) { this._Version = value;}

tns_testexecution.prototype.setVersion = tns_testexecution_setVersion;
//
// accessor is tns_testexecution.prototype.getResult
// element get for Result
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Result
// setter function is is tns_testexecution.prototype.setResult
//
function tns_testexecution_getResult() { return this._Result;}

tns_testexecution.prototype.getResult = tns_testexecution_getResult;

function tns_testexecution_setResult(value) { this._Result = value;}

tns_testexecution.prototype.setResult = tns_testexecution_setResult;
//
// accessor is tns_testexecution.prototype.getRecordCreated
// element get for RecordCreated
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordCreated
// setter function is is tns_testexecution.prototype.setRecordCreated
//
function tns_testexecution_getRecordCreated() { return this._RecordCreated;}

tns_testexecution.prototype.getRecordCreated = tns_testexecution_getRecordCreated;

function tns_testexecution_setRecordCreated(value) { this._RecordCreated = value;}

tns_testexecution.prototype.setRecordCreated = tns_testexecution_setRecordCreated;
//
// accessor is tns_testexecution.prototype.getRecordModified
// element get for RecordModified
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordModified
// setter function is is tns_testexecution.prototype.setRecordModified
//
function tns_testexecution_getRecordModified() { return this._RecordModified;}

tns_testexecution.prototype.getRecordModified = tns_testexecution_getRecordModified;

function tns_testexecution_setRecordModified(value) { this._RecordModified = value;}

tns_testexecution.prototype.setRecordModified = tns_testexecution_setRecordModified;
//
// accessor is tns_testexecution.prototype.getId
// element get for Id
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Id
// setter function is is tns_testexecution.prototype.setId
//
function tns_testexecution_getId() { return this._Id;}

tns_testexecution.prototype.getId = tns_testexecution_getId;

function tns_testexecution_setId(value) { this._Id = value;}

tns_testexecution.prototype.setId = tns_testexecution_setId;
//
// accessor is tns_testexecution.prototype.getClassName
// element get for ClassName
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ClassName
// setter function is is tns_testexecution.prototype.setClassName
//
function tns_testexecution_getClassName() { return this._ClassName;}

tns_testexecution.prototype.getClassName = tns_testexecution_getClassName;

function tns_testexecution_setClassName(value) { this._ClassName = value;}

tns_testexecution.prototype.setClassName = tns_testexecution_setClassName;
//
// Serialize {tns}testexecution
//
function tns_testexecution_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:TestScenario>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._TestScenario);
     xml = xml + '</jns0:TestScenario>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Environment>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Environment);
     xml = xml + '</jns0:Environment>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Version>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Version);
     xml = xml + '</jns0:Version>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Result>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Result);
     xml = xml + '</jns0:Result>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordCreated>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordCreated);
     xml = xml + '</jns0:RecordCreated>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordModified>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordModified);
     xml = xml + '</jns0:RecordModified>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Id>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Id);
     xml = xml + '</jns0:Id>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ClassName>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ClassName);
     xml = xml + '</jns0:ClassName>';
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_testexecution.prototype.serialize = tns_testexecution_serialize;

function tns_testexecution_deserialize (cxfjsutils, element) {
    var newobject = new tns_testexecution();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing TestScenario');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setTestScenario(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Environment');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setEnvironment(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Version');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setVersion(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Result');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setResult(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordCreated');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordCreated(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordModified');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordModified(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Id');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setId(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ClassName');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setClassName(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}attachment
//
function tns_attachment () {
    this.typeMarker = 'tns_attachment';
    this._File = '';
    this._FileExt = '';
    this._FilePath = '';
    this._Description = '';
    this._ObjectId = 0;
    this._ObjectClass = '';
    this._RecordCreated = '';
    this._RecordModified = '';
    this._Id = 0;
    this._ClassName = '';
}

//
// accessor is tns_attachment.prototype.getFile
// element get for File
// - element type is {http://www.w3.org/2001/XMLSchema}base64Binary
// - required element
//
// element set for File
// setter function is is tns_attachment.prototype.setFile
//
function tns_attachment_getFile() { return this._File;}

tns_attachment.prototype.getFile = tns_attachment_getFile;

function tns_attachment_setFile(value) { this._File = value;}

tns_attachment.prototype.setFile = tns_attachment_setFile;
//
// accessor is tns_attachment.prototype.getFileExt
// element get for FileExt
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for FileExt
// setter function is is tns_attachment.prototype.setFileExt
//
function tns_attachment_getFileExt() { return this._FileExt;}

tns_attachment.prototype.getFileExt = tns_attachment_getFileExt;

function tns_attachment_setFileExt(value) { this._FileExt = value;}

tns_attachment.prototype.setFileExt = tns_attachment_setFileExt;
//
// accessor is tns_attachment.prototype.getFilePath
// element get for FilePath
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for FilePath
// setter function is is tns_attachment.prototype.setFilePath
//
function tns_attachment_getFilePath() { return this._FilePath;}

tns_attachment.prototype.getFilePath = tns_attachment_getFilePath;

function tns_attachment_setFilePath(value) { this._FilePath = value;}

tns_attachment.prototype.setFilePath = tns_attachment_setFilePath;
//
// accessor is tns_attachment.prototype.getDescription
// element get for Description
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Description
// setter function is is tns_attachment.prototype.setDescription
//
function tns_attachment_getDescription() { return this._Description;}

tns_attachment.prototype.getDescription = tns_attachment_getDescription;

function tns_attachment_setDescription(value) { this._Description = value;}

tns_attachment.prototype.setDescription = tns_attachment_setDescription;
//
// accessor is tns_attachment.prototype.getObjectId
// element get for ObjectId
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for ObjectId
// setter function is is tns_attachment.prototype.setObjectId
//
function tns_attachment_getObjectId() { return this._ObjectId;}

tns_attachment.prototype.getObjectId = tns_attachment_getObjectId;

function tns_attachment_setObjectId(value) { this._ObjectId = value;}

tns_attachment.prototype.setObjectId = tns_attachment_setObjectId;
//
// accessor is tns_attachment.prototype.getObjectClass
// element get for ObjectClass
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ObjectClass
// setter function is is tns_attachment.prototype.setObjectClass
//
function tns_attachment_getObjectClass() { return this._ObjectClass;}

tns_attachment.prototype.getObjectClass = tns_attachment_getObjectClass;

function tns_attachment_setObjectClass(value) { this._ObjectClass = value;}

tns_attachment.prototype.setObjectClass = tns_attachment_setObjectClass;
//
// accessor is tns_attachment.prototype.getRecordCreated
// element get for RecordCreated
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordCreated
// setter function is is tns_attachment.prototype.setRecordCreated
//
function tns_attachment_getRecordCreated() { return this._RecordCreated;}

tns_attachment.prototype.getRecordCreated = tns_attachment_getRecordCreated;

function tns_attachment_setRecordCreated(value) { this._RecordCreated = value;}

tns_attachment.prototype.setRecordCreated = tns_attachment_setRecordCreated;
//
// accessor is tns_attachment.prototype.getRecordModified
// element get for RecordModified
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordModified
// setter function is is tns_attachment.prototype.setRecordModified
//
function tns_attachment_getRecordModified() { return this._RecordModified;}

tns_attachment.prototype.getRecordModified = tns_attachment_getRecordModified;

function tns_attachment_setRecordModified(value) { this._RecordModified = value;}

tns_attachment.prototype.setRecordModified = tns_attachment_setRecordModified;
//
// accessor is tns_attachment.prototype.getId
// element get for Id
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Id
// setter function is is tns_attachment.prototype.setId
//
function tns_attachment_getId() { return this._Id;}

tns_attachment.prototype.getId = tns_attachment_getId;

function tns_attachment_setId(value) { this._Id = value;}

tns_attachment.prototype.setId = tns_attachment_setId;
//
// accessor is tns_attachment.prototype.getClassName
// element get for ClassName
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ClassName
// setter function is is tns_attachment.prototype.setClassName
//
function tns_attachment_getClassName() { return this._ClassName;}

tns_attachment.prototype.getClassName = tns_attachment_getClassName;

function tns_attachment_setClassName(value) { this._ClassName = value;}

tns_attachment.prototype.setClassName = tns_attachment_setClassName;
//
// Serialize {tns}attachment
//
function tns_attachment_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:File>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._File);
     xml = xml + '</jns0:File>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:FileExt>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._FileExt);
     xml = xml + '</jns0:FileExt>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:FilePath>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._FilePath);
     xml = xml + '</jns0:FilePath>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Description>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Description);
     xml = xml + '</jns0:Description>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ObjectId>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ObjectId);
     xml = xml + '</jns0:ObjectId>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ObjectClass>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ObjectClass);
     xml = xml + '</jns0:ObjectClass>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordCreated>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordCreated);
     xml = xml + '</jns0:RecordCreated>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordModified>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordModified);
     xml = xml + '</jns0:RecordModified>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Id>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Id);
     xml = xml + '</jns0:Id>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ClassName>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ClassName);
     xml = xml + '</jns0:ClassName>';
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_attachment.prototype.serialize = tns_attachment_serialize;

function tns_attachment_deserialize (cxfjsutils, element) {
    var newobject = new tns_attachment();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing File');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = cxfjsutils.deserializeBase64orMom(curElement);
    }
    newobject.setFile(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing FileExt');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setFileExt(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing FilePath');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setFilePath(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Description');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setDescription(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ObjectId');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setObjectId(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ObjectClass');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setObjectClass(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordCreated');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordCreated(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordModified');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordModified(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Id');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setId(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ClassName');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setClassName(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}ReportResultResponseType
//
function tns_ReportResultResponseType () {
    this.typeMarker = 'tns_ReportResultResponseType';
}

//
// Serialize {tns}ReportResultResponseType
//
function tns_ReportResultResponseType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_ReportResultResponseType.prototype.serialize = tns_ReportResultResponseType_serialize;

function tns_ReportResultResponseType_deserialize (cxfjsutils, element) {
    var newobject = new tns_ReportResultResponseType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    return newobject;
}

//
// Constructor for XML Schema item {tns}request
//
function tns_request () {
    this.typeMarker = 'tns_request';
    this._Caption = '';
    this._Description = '';
    this._Type = 0;
    this._Function = 0;
    this._Environment = 0;
    this._SubmittedVersion = '';
    this._Priority = 0;
    this._State = '';
    this._TestCaseExecution = 0;
    this._Author = 0;
    this._Owner = 0;
    this._Project = 0;
    this._ClosedInVersion = '';
    this._Estimation = 0;
    this._PlannedRelease = 0;
    this._RecordCreated = '';
    this._RecordModified = '';
    this._Id = 0;
    this._ClassName = '';
}

//
// accessor is tns_request.prototype.getCaption
// element get for Caption
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Caption
// setter function is is tns_request.prototype.setCaption
//
function tns_request_getCaption() { return this._Caption;}

tns_request.prototype.getCaption = tns_request_getCaption;

function tns_request_setCaption(value) { this._Caption = value;}

tns_request.prototype.setCaption = tns_request_setCaption;
//
// accessor is tns_request.prototype.getDescription
// element get for Description
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for Description
// setter function is is tns_request.prototype.setDescription
//
function tns_request_getDescription() { return this._Description;}

tns_request.prototype.getDescription = tns_request_getDescription;

function tns_request_setDescription(value) { this._Description = value;}

tns_request.prototype.setDescription = tns_request_setDescription;
//
// accessor is tns_request.prototype.getType
// element get for Type
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Type
// setter function is is tns_request.prototype.setType
//
function tns_request_getType() { return this._Type;}

tns_request.prototype.getType = tns_request_getType;

function tns_request_setType(value) { this._Type = value;}

tns_request.prototype.setType = tns_request_setType;
//
// accessor is tns_request.prototype.getFunction
// element get for Function
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Function
// setter function is is tns_request.prototype.setFunction
//
function tns_request_getFunction() { return this._Function;}

tns_request.prototype.getFunction = tns_request_getFunction;

function tns_request_setFunction(value) { this._Function = value;}

tns_request.prototype.setFunction = tns_request_setFunction;
//
// accessor is tns_request.prototype.getEnvironment
// element get for Environment
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Environment
// setter function is is tns_request.prototype.setEnvironment
//
function tns_request_getEnvironment() { return this._Environment;}

tns_request.prototype.getEnvironment = tns_request_getEnvironment;

function tns_request_setEnvironment(value) { this._Environment = value;}

tns_request.prototype.setEnvironment = tns_request_setEnvironment;
//
// accessor is tns_request.prototype.getSubmittedVersion
// element get for SubmittedVersion
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for SubmittedVersion
// setter function is is tns_request.prototype.setSubmittedVersion
//
function tns_request_getSubmittedVersion() { return this._SubmittedVersion;}

tns_request.prototype.getSubmittedVersion = tns_request_getSubmittedVersion;

function tns_request_setSubmittedVersion(value) { this._SubmittedVersion = value;}

tns_request.prototype.setSubmittedVersion = tns_request_setSubmittedVersion;
//
// accessor is tns_request.prototype.getPriority
// element get for Priority
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Priority
// setter function is is tns_request.prototype.setPriority
//
function tns_request_getPriority() { return this._Priority;}

tns_request.prototype.getPriority = tns_request_getPriority;

function tns_request_setPriority(value) { this._Priority = value;}

tns_request.prototype.setPriority = tns_request_setPriority;
//
// accessor is tns_request.prototype.getState
// element get for State
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for State
// setter function is is tns_request.prototype.setState
//
function tns_request_getState() { return this._State;}

tns_request.prototype.getState = tns_request_getState;

function tns_request_setState(value) { this._State = value;}

tns_request.prototype.setState = tns_request_setState;
//
// accessor is tns_request.prototype.getTestCaseExecution
// element get for TestCaseExecution
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for TestCaseExecution
// setter function is is tns_request.prototype.setTestCaseExecution
//
function tns_request_getTestCaseExecution() { return this._TestCaseExecution;}

tns_request.prototype.getTestCaseExecution = tns_request_getTestCaseExecution;

function tns_request_setTestCaseExecution(value) { this._TestCaseExecution = value;}

tns_request.prototype.setTestCaseExecution = tns_request_setTestCaseExecution;
//
// accessor is tns_request.prototype.getAuthor
// element get for Author
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Author
// setter function is is tns_request.prototype.setAuthor
//
function tns_request_getAuthor() { return this._Author;}

tns_request.prototype.getAuthor = tns_request_getAuthor;

function tns_request_setAuthor(value) { this._Author = value;}

tns_request.prototype.setAuthor = tns_request_setAuthor;
//
// accessor is tns_request.prototype.getOwner
// element get for Owner
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Owner
// setter function is is tns_request.prototype.setOwner
//
function tns_request_getOwner() { return this._Owner;}

tns_request.prototype.getOwner = tns_request_getOwner;

function tns_request_setOwner(value) { this._Owner = value;}

tns_request.prototype.setOwner = tns_request_setOwner;
//
// accessor is tns_request.prototype.getProject
// element get for Project
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Project
// setter function is is tns_request.prototype.setProject
//
function tns_request_getProject() { return this._Project;}

tns_request.prototype.getProject = tns_request_getProject;

function tns_request_setProject(value) { this._Project = value;}

tns_request.prototype.setProject = tns_request_setProject;
//
// accessor is tns_request.prototype.getClosedInVersion
// element get for ClosedInVersion
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ClosedInVersion
// setter function is is tns_request.prototype.setClosedInVersion
//
function tns_request_getClosedInVersion() { return this._ClosedInVersion;}

tns_request.prototype.getClosedInVersion = tns_request_getClosedInVersion;

function tns_request_setClosedInVersion(value) { this._ClosedInVersion = value;}

tns_request.prototype.setClosedInVersion = tns_request_setClosedInVersion;
//
// accessor is tns_request.prototype.getEstimation
// element get for Estimation
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Estimation
// setter function is is tns_request.prototype.setEstimation
//
function tns_request_getEstimation() { return this._Estimation;}

tns_request.prototype.getEstimation = tns_request_getEstimation;

function tns_request_setEstimation(value) { this._Estimation = value;}

tns_request.prototype.setEstimation = tns_request_setEstimation;
//
// accessor is tns_request.prototype.getPlannedRelease
// element get for PlannedRelease
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for PlannedRelease
// setter function is is tns_request.prototype.setPlannedRelease
//
function tns_request_getPlannedRelease() { return this._PlannedRelease;}

tns_request.prototype.getPlannedRelease = tns_request_getPlannedRelease;

function tns_request_setPlannedRelease(value) { this._PlannedRelease = value;}

tns_request.prototype.setPlannedRelease = tns_request_setPlannedRelease;
//
// accessor is tns_request.prototype.getRecordCreated
// element get for RecordCreated
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordCreated
// setter function is is tns_request.prototype.setRecordCreated
//
function tns_request_getRecordCreated() { return this._RecordCreated;}

tns_request.prototype.getRecordCreated = tns_request_getRecordCreated;

function tns_request_setRecordCreated(value) { this._RecordCreated = value;}

tns_request.prototype.setRecordCreated = tns_request_setRecordCreated;
//
// accessor is tns_request.prototype.getRecordModified
// element get for RecordModified
// - element type is {http://www.w3.org/2001/XMLSchema}dateTime
// - required element
//
// element set for RecordModified
// setter function is is tns_request.prototype.setRecordModified
//
function tns_request_getRecordModified() { return this._RecordModified;}

tns_request.prototype.getRecordModified = tns_request_getRecordModified;

function tns_request_setRecordModified(value) { this._RecordModified = value;}

tns_request.prototype.setRecordModified = tns_request_setRecordModified;
//
// accessor is tns_request.prototype.getId
// element get for Id
// - element type is {http://www.w3.org/2001/XMLSchema}int
// - required element
//
// element set for Id
// setter function is is tns_request.prototype.setId
//
function tns_request_getId() { return this._Id;}

tns_request.prototype.getId = tns_request_getId;

function tns_request_setId(value) { this._Id = value;}

tns_request.prototype.setId = tns_request_setId;
//
// accessor is tns_request.prototype.getClassName
// element get for ClassName
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for ClassName
// setter function is is tns_request.prototype.setClassName
//
function tns_request_getClassName() { return this._ClassName;}

tns_request.prototype.getClassName = tns_request_getClassName;

function tns_request_setClassName(value) { this._ClassName = value;}

tns_request.prototype.setClassName = tns_request_setClassName;
//
// Serialize {tns}request
//
function tns_request_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:Caption>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Caption);
     xml = xml + '</jns0:Caption>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Description>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Description);
     xml = xml + '</jns0:Description>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Type>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Type);
     xml = xml + '</jns0:Type>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Function>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Function);
     xml = xml + '</jns0:Function>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Environment>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Environment);
     xml = xml + '</jns0:Environment>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:SubmittedVersion>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._SubmittedVersion);
     xml = xml + '</jns0:SubmittedVersion>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Priority>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Priority);
     xml = xml + '</jns0:Priority>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:State>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._State);
     xml = xml + '</jns0:State>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:TestCaseExecution>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._TestCaseExecution);
     xml = xml + '</jns0:TestCaseExecution>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Author>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Author);
     xml = xml + '</jns0:Author>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Owner>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Owner);
     xml = xml + '</jns0:Owner>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Project>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Project);
     xml = xml + '</jns0:Project>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ClosedInVersion>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ClosedInVersion);
     xml = xml + '</jns0:ClosedInVersion>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Estimation>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Estimation);
     xml = xml + '</jns0:Estimation>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:PlannedRelease>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._PlannedRelease);
     xml = xml + '</jns0:PlannedRelease>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordCreated>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordCreated);
     xml = xml + '</jns0:RecordCreated>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:RecordModified>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._RecordModified);
     xml = xml + '</jns0:RecordModified>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:Id>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._Id);
     xml = xml + '</jns0:Id>';
    }
    // block for local variables
    {
     xml = xml + '<jns0:ClassName>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._ClassName);
     xml = xml + '</jns0:ClassName>';
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_request.prototype.serialize = tns_request_serialize;

function tns_request_deserialize (cxfjsutils, element) {
    var newobject = new tns_request();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Caption');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setCaption(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Description');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setDescription(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Type');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setType(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Function');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setFunction(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Environment');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setEnvironment(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing SubmittedVersion');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setSubmittedVersion(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Priority');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setPriority(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing State');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setState(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing TestCaseExecution');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setTestCaseExecution(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Author');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setAuthor(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Owner');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setOwner(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Project');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setProject(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ClosedInVersion');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setClosedInVersion(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Estimation');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setEstimation(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing PlannedRelease');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setPlannedRelease(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordCreated');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordCreated(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing RecordModified');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setRecordModified(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing Id');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = parseInt(value);
    }
    newobject.setId(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing ClassName');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setClassName(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}requestArray
//
function tns_requestArray () {
    this.typeMarker = 'tns_requestArray';
}

//
// Serialize {tns}requestArray
//
function tns_requestArray_serialize(cxfjsutils, elementName, extraNamespaces) {
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
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_requestArray.prototype.serialize = tns_requestArray_serialize;

function tns_requestArray_deserialize (cxfjsutils, element) {
    var newobject = new tns_requestArray();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    return newobject;
}

//
// Constructor for XML Schema item {tns}RunResponseType
//
function tns_RunResponseType () {
    this.typeMarker = 'tns_RunResponseType';
    this._return = null;
}

//
// accessor is tns_RunResponseType.prototype.getReturn
// element get for return
// - element type is {tns}testexecution
// - required element
//
// element set for return
// setter function is is tns_RunResponseType.prototype.setReturn
//
function tns_RunResponseType_getReturn() { return this._return;}

tns_RunResponseType.prototype.getReturn = tns_RunResponseType_getReturn;

function tns_RunResponseType_setReturn(value) { this._return = value;}

tns_RunResponseType.prototype.setReturn = tns_RunResponseType_setReturn;
//
// Serialize {tns}RunResponseType
//
function tns_RunResponseType_serialize(cxfjsutils, elementName, extraNamespaces) {
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

tns_RunResponseType.prototype.serialize = tns_RunResponseType_serialize;

function tns_RunResponseType_deserialize (cxfjsutils, element) {
    var newobject = new tns_RunResponseType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing return');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testexecution_deserialize(cxfjsutils, curElement);
    }
    newobject.setReturn(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}FindRequestType
//
function tns_FindRequestType () {
    this.typeMarker = 'tns_FindRequestType';
    this._token = '';
    this._object = null;
}

//
// accessor is tns_FindRequestType.prototype.getToken
// element get for token
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for token
// setter function is is tns_FindRequestType.prototype.setToken
//
function tns_FindRequestType_getToken() { return this._token;}

tns_FindRequestType.prototype.getToken = tns_FindRequestType_getToken;

function tns_FindRequestType_setToken(value) { this._token = value;}

tns_FindRequestType.prototype.setToken = tns_FindRequestType_setToken;
//
// accessor is tns_FindRequestType.prototype.getObject
// element get for object
// - element type is {tns}testscenario
// - required element
//
// element set for object
// setter function is is tns_FindRequestType.prototype.setObject
//
function tns_FindRequestType_getObject() { return this._object;}

tns_FindRequestType.prototype.getObject = tns_FindRequestType_getObject;

function tns_FindRequestType_setObject(value) { this._object = value;}

tns_FindRequestType.prototype.setObject = tns_FindRequestType_setObject;
//
// Serialize {tns}FindRequestType
//
function tns_FindRequestType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:token>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._token);
     xml = xml + '</jns0:token>';
    }
    // block for local variables
    {
     xml = xml + this._object.serialize(cxfjsutils, 'jns0:object', null);
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_FindRequestType.prototype.serialize = tns_FindRequestType_serialize;

function tns_FindRequestType_deserialize (cxfjsutils, element) {
    var newobject = new tns_FindRequestType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing token');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setToken(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing object');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setObject(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}GetResultRequestType
//
function tns_GetResultRequestType () {
    this.typeMarker = 'tns_GetResultRequestType';
    this._token = '';
    this._execution = null;
}

//
// accessor is tns_GetResultRequestType.prototype.getToken
// element get for token
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for token
// setter function is is tns_GetResultRequestType.prototype.setToken
//
function tns_GetResultRequestType_getToken() { return this._token;}

tns_GetResultRequestType.prototype.getToken = tns_GetResultRequestType_getToken;

function tns_GetResultRequestType_setToken(value) { this._token = value;}

tns_GetResultRequestType.prototype.setToken = tns_GetResultRequestType_setToken;
//
// accessor is tns_GetResultRequestType.prototype.getExecution
// element get for execution
// - element type is {tns}testexecution
// - required element
//
// element set for execution
// setter function is is tns_GetResultRequestType.prototype.setExecution
//
function tns_GetResultRequestType_getExecution() { return this._execution;}

tns_GetResultRequestType.prototype.getExecution = tns_GetResultRequestType_getExecution;

function tns_GetResultRequestType_setExecution(value) { this._execution = value;}

tns_GetResultRequestType.prototype.setExecution = tns_GetResultRequestType_setExecution;
//
// Serialize {tns}GetResultRequestType
//
function tns_GetResultRequestType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:token>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._token);
     xml = xml + '</jns0:token>';
    }
    // block for local variables
    {
     xml = xml + this._execution.serialize(cxfjsutils, 'jns0:execution', null);
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_GetResultRequestType.prototype.serialize = tns_GetResultRequestType_serialize;

function tns_GetResultRequestType_deserialize (cxfjsutils, element) {
    var newobject = new tns_GetResultRequestType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing token');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setToken(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing execution');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testexecution_deserialize(cxfjsutils, curElement);
    }
    newobject.setExecution(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}environmentArray
//
function tns_environmentArray () {
    this.typeMarker = 'tns_environmentArray';
}

//
// Serialize {tns}environmentArray
//
function tns_environmentArray_serialize(cxfjsutils, elementName, extraNamespaces) {
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
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_environmentArray.prototype.serialize = tns_environmentArray_serialize;

function tns_environmentArray_deserialize (cxfjsutils, element) {
    var newobject = new tns_environmentArray();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    return newobject;
}

//
// Constructor for XML Schema item {tns}ReportFileRequestType
//
function tns_ReportFileRequestType () {
    this.typeMarker = 'tns_ReportFileRequestType';
    this._token = '';
    this._execution = null;
    this._test = null;
    this._file = null;
}

//
// accessor is tns_ReportFileRequestType.prototype.getToken
// element get for token
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for token
// setter function is is tns_ReportFileRequestType.prototype.setToken
//
function tns_ReportFileRequestType_getToken() { return this._token;}

tns_ReportFileRequestType.prototype.getToken = tns_ReportFileRequestType_getToken;

function tns_ReportFileRequestType_setToken(value) { this._token = value;}

tns_ReportFileRequestType.prototype.setToken = tns_ReportFileRequestType_setToken;
//
// accessor is tns_ReportFileRequestType.prototype.getExecution
// element get for execution
// - element type is {tns}testexecution
// - required element
//
// element set for execution
// setter function is is tns_ReportFileRequestType.prototype.setExecution
//
function tns_ReportFileRequestType_getExecution() { return this._execution;}

tns_ReportFileRequestType.prototype.getExecution = tns_ReportFileRequestType_getExecution;

function tns_ReportFileRequestType_setExecution(value) { this._execution = value;}

tns_ReportFileRequestType.prototype.setExecution = tns_ReportFileRequestType_setExecution;
//
// accessor is tns_ReportFileRequestType.prototype.getTest
// element get for test
// - element type is {tns}testscenario
// - required element
//
// element set for test
// setter function is is tns_ReportFileRequestType.prototype.setTest
//
function tns_ReportFileRequestType_getTest() { return this._test;}

tns_ReportFileRequestType.prototype.getTest = tns_ReportFileRequestType_getTest;

function tns_ReportFileRequestType_setTest(value) { this._test = value;}

tns_ReportFileRequestType.prototype.setTest = tns_ReportFileRequestType_setTest;
//
// accessor is tns_ReportFileRequestType.prototype.getFile
// element get for file
// - element type is {tns}attachment
// - required element
//
// element set for file
// setter function is is tns_ReportFileRequestType.prototype.setFile
//
function tns_ReportFileRequestType_getFile() { return this._file;}

tns_ReportFileRequestType.prototype.getFile = tns_ReportFileRequestType_getFile;

function tns_ReportFileRequestType_setFile(value) { this._file = value;}

tns_ReportFileRequestType.prototype.setFile = tns_ReportFileRequestType_setFile;
//
// Serialize {tns}ReportFileRequestType
//
function tns_ReportFileRequestType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:token>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._token);
     xml = xml + '</jns0:token>';
    }
    // block for local variables
    {
     xml = xml + this._execution.serialize(cxfjsutils, 'jns0:execution', null);
    }
    // block for local variables
    {
     xml = xml + this._test.serialize(cxfjsutils, 'jns0:test', null);
    }
    // block for local variables
    {
     xml = xml + this._file.serialize(cxfjsutils, 'jns0:file', null);
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_ReportFileRequestType.prototype.serialize = tns_ReportFileRequestType_serialize;

function tns_ReportFileRequestType_deserialize (cxfjsutils, element) {
    var newobject = new tns_ReportFileRequestType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing token');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setToken(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing execution');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testexecution_deserialize(cxfjsutils, curElement);
    }
    newobject.setExecution(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing test');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setTest(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing file');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_attachment_deserialize(cxfjsutils, curElement);
    }
    newobject.setFile(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}testexecutionArray
//
function tns_testexecutionArray () {
    this.typeMarker = 'tns_testexecutionArray';
}

//
// Serialize {tns}testexecutionArray
//
function tns_testexecutionArray_serialize(cxfjsutils, elementName, extraNamespaces) {
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
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_testexecutionArray.prototype.serialize = tns_testexecutionArray_serialize;

function tns_testexecutionArray_deserialize (cxfjsutils, element) {
    var newobject = new tns_testexecutionArray();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    return newobject;
}

//
// Constructor for XML Schema item {tns}ReportResultRequestType
//
function tns_ReportResultRequestType () {
    this.typeMarker = 'tns_ReportResultRequestType';
    this._token = '';
    this._execution = null;
    this._test = null;
    this._result = null;
    this._description = '';
}

//
// accessor is tns_ReportResultRequestType.prototype.getToken
// element get for token
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for token
// setter function is is tns_ReportResultRequestType.prototype.setToken
//
function tns_ReportResultRequestType_getToken() { return this._token;}

tns_ReportResultRequestType.prototype.getToken = tns_ReportResultRequestType_getToken;

function tns_ReportResultRequestType_setToken(value) { this._token = value;}

tns_ReportResultRequestType.prototype.setToken = tns_ReportResultRequestType_setToken;
//
// accessor is tns_ReportResultRequestType.prototype.getExecution
// element get for execution
// - element type is {tns}testexecution
// - required element
//
// element set for execution
// setter function is is tns_ReportResultRequestType.prototype.setExecution
//
function tns_ReportResultRequestType_getExecution() { return this._execution;}

tns_ReportResultRequestType.prototype.getExecution = tns_ReportResultRequestType_getExecution;

function tns_ReportResultRequestType_setExecution(value) { this._execution = value;}

tns_ReportResultRequestType.prototype.setExecution = tns_ReportResultRequestType_setExecution;
//
// accessor is tns_ReportResultRequestType.prototype.getTest
// element get for test
// - element type is {tns}testscenario
// - required element
//
// element set for test
// setter function is is tns_ReportResultRequestType.prototype.setTest
//
function tns_ReportResultRequestType_getTest() { return this._test;}

tns_ReportResultRequestType.prototype.getTest = tns_ReportResultRequestType_getTest;

function tns_ReportResultRequestType_setTest(value) { this._test = value;}

tns_ReportResultRequestType.prototype.setTest = tns_ReportResultRequestType_setTest;
//
// accessor is tns_ReportResultRequestType.prototype.getResult
// element get for result
// - element type is {tns}testexecutionresult
// - required element
//
// element set for result
// setter function is is tns_ReportResultRequestType.prototype.setResult
//
function tns_ReportResultRequestType_getResult() { return this._result;}

tns_ReportResultRequestType.prototype.getResult = tns_ReportResultRequestType_getResult;

function tns_ReportResultRequestType_setResult(value) { this._result = value;}

tns_ReportResultRequestType.prototype.setResult = tns_ReportResultRequestType_setResult;
//
// accessor is tns_ReportResultRequestType.prototype.getDescription
// element get for description
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for description
// setter function is is tns_ReportResultRequestType.prototype.setDescription
//
function tns_ReportResultRequestType_getDescription() { return this._description;}

tns_ReportResultRequestType.prototype.getDescription = tns_ReportResultRequestType_getDescription;

function tns_ReportResultRequestType_setDescription(value) { this._description = value;}

tns_ReportResultRequestType.prototype.setDescription = tns_ReportResultRequestType_setDescription;
//
// Serialize {tns}ReportResultRequestType
//
function tns_ReportResultRequestType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:token>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._token);
     xml = xml + '</jns0:token>';
    }
    // block for local variables
    {
     xml = xml + this._execution.serialize(cxfjsutils, 'jns0:execution', null);
    }
    // block for local variables
    {
     xml = xml + this._test.serialize(cxfjsutils, 'jns0:test', null);
    }
    // block for local variables
    {
     xml = xml + this._result.serialize(cxfjsutils, 'jns0:result', null);
    }
    // block for local variables
    {
     xml = xml + '<jns0:description>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._description);
     xml = xml + '</jns0:description>';
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_ReportResultRequestType.prototype.serialize = tns_ReportResultRequestType_serialize;

function tns_ReportResultRequestType_deserialize (cxfjsutils, element) {
    var newobject = new tns_ReportResultRequestType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing token');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setToken(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing execution');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testexecution_deserialize(cxfjsutils, curElement);
    }
    newobject.setExecution(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing test');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setTest(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing result');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testexecutionresult_deserialize(cxfjsutils, curElement);
    }
    newobject.setResult(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing description');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setDescription(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Constructor for XML Schema item {tns}AppendRequestType
//
function tns_AppendRequestType () {
    this.typeMarker = 'tns_AppendRequestType';
    this._token = '';
    this._parent = null;
    this._object = null;
}

//
// accessor is tns_AppendRequestType.prototype.getToken
// element get for token
// - element type is {http://www.w3.org/2001/XMLSchema}string
// - required element
//
// element set for token
// setter function is is tns_AppendRequestType.prototype.setToken
//
function tns_AppendRequestType_getToken() { return this._token;}

tns_AppendRequestType.prototype.getToken = tns_AppendRequestType_getToken;

function tns_AppendRequestType_setToken(value) { this._token = value;}

tns_AppendRequestType.prototype.setToken = tns_AppendRequestType_setToken;
//
// accessor is tns_AppendRequestType.prototype.getParent
// element get for parent
// - element type is {tns}testscenario
// - required element
//
// element set for parent
// setter function is is tns_AppendRequestType.prototype.setParent
//
function tns_AppendRequestType_getParent() { return this._parent;}

tns_AppendRequestType.prototype.getParent = tns_AppendRequestType_getParent;

function tns_AppendRequestType_setParent(value) { this._parent = value;}

tns_AppendRequestType.prototype.setParent = tns_AppendRequestType_setParent;
//
// accessor is tns_AppendRequestType.prototype.getObject
// element get for object
// - element type is {tns}testscenario
// - required element
//
// element set for object
// setter function is is tns_AppendRequestType.prototype.setObject
//
function tns_AppendRequestType_getObject() { return this._object;}

tns_AppendRequestType.prototype.getObject = tns_AppendRequestType_getObject;

function tns_AppendRequestType_setObject(value) { this._object = value;}

tns_AppendRequestType.prototype.setObject = tns_AppendRequestType_setObject;
//
// Serialize {tns}AppendRequestType
//
function tns_AppendRequestType_serialize(cxfjsutils, elementName, extraNamespaces) {
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
     xml = xml + '<jns0:token>';
     xml = xml + cxfjsutils.escapeXmlEntities(this._token);
     xml = xml + '</jns0:token>';
    }
    // block for local variables
    {
     xml = xml + this._parent.serialize(cxfjsutils, 'jns0:parent', null);
    }
    // block for local variables
    {
     xml = xml + this._object.serialize(cxfjsutils, 'jns0:object', null);
    }
    if (elementName != null) {
     xml = xml + '</';
     xml = xml + elementName;
     xml = xml + '>';
    }
    return xml;
}

tns_AppendRequestType.prototype.serialize = tns_AppendRequestType_serialize;

function tns_AppendRequestType_deserialize (cxfjsutils, element) {
    var newobject = new tns_AppendRequestType();
    cxfjsutils.trace('element: ' + cxfjsutils.traceElementName(element));
    var curElement = cxfjsutils.getFirstElementChild(element);
    var item;
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing token');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     value = cxfjsutils.getNodeText(curElement);
     item = value;
    }
    newobject.setToken(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing parent');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setParent(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    cxfjsutils.trace('curElement: ' + cxfjsutils.traceElementName(curElement));
    cxfjsutils.trace('processing object');
    var value = null;
    if (!cxfjsutils.isElementNil(curElement)) {
     item = tns_testscenario_deserialize(cxfjsutils, curElement);
    }
    newobject.setObject(item);
    var item = null;
    if (curElement != null) {
     curElement = cxfjsutils.getNextElementSibling(curElement);
    }
    return newobject;
}

//
// Definitions for service: {tns}TestService
//

// Javascript for {tns}TestServicePortType

function tns_TestServicePortType () {
    this.jsutils = new CxfApacheOrgUtil();
    this.jsutils.interfaceObject = this;
    this.synchronous = false;
    this.url = null;
    this.client = null;
    this.response = null;
    this.globalElementSerializers = [];
    this.globalElementDeserializers = [];
    this.globalElementSerializers['{tns}RunResponseType'] = tns_RunResponseType_serialize;
    this.globalElementDeserializers['{tns}RunResponseType'] = tns_RunResponseType_deserialize;
    this.globalElementSerializers['{tns}ReportIssueResponseType'] = tns_ReportIssueResponseType_serialize;
    this.globalElementDeserializers['{tns}ReportIssueResponseType'] = tns_ReportIssueResponseType_deserialize;
    this.globalElementSerializers['{tns}CreateRequestType'] = tns_CreateRequestType_serialize;
    this.globalElementDeserializers['{tns}CreateRequestType'] = tns_CreateRequestType_deserialize;
    this.globalElementSerializers['{tns}GetResultResponseType'] = tns_GetResultResponseType_serialize;
    this.globalElementDeserializers['{tns}GetResultResponseType'] = tns_GetResultResponseType_deserialize;
    this.globalElementSerializers['{tns}ReportResultResponseType'] = tns_ReportResultResponseType_serialize;
    this.globalElementDeserializers['{tns}ReportResultResponseType'] = tns_ReportResultResponseType_deserialize;
    this.globalElementSerializers['{tns}RunRequestType'] = tns_RunRequestType_serialize;
    this.globalElementDeserializers['{tns}RunRequestType'] = tns_RunRequestType_deserialize;
    this.globalElementSerializers['{tns}ReportResultRequestType'] = tns_ReportResultRequestType_serialize;
    this.globalElementDeserializers['{tns}ReportResultRequestType'] = tns_ReportResultRequestType_deserialize;
    this.globalElementSerializers['{tns}ReportFileResponseType'] = tns_ReportFileResponseType_serialize;
    this.globalElementDeserializers['{tns}ReportFileResponseType'] = tns_ReportFileResponseType_deserialize;
    this.globalElementSerializers['{tns}ReportFileRequestType'] = tns_ReportFileRequestType_serialize;
    this.globalElementDeserializers['{tns}ReportFileRequestType'] = tns_ReportFileRequestType_deserialize;
    this.globalElementSerializers['{tns}CreateResponseType'] = tns_CreateResponseType_serialize;
    this.globalElementDeserializers['{tns}CreateResponseType'] = tns_CreateResponseType_deserialize;
    this.globalElementSerializers['{tns}FindResponseType'] = tns_FindResponseType_serialize;
    this.globalElementDeserializers['{tns}FindResponseType'] = tns_FindResponseType_deserialize;
    this.globalElementSerializers['{tns}FindRequestType'] = tns_FindRequestType_serialize;
    this.globalElementDeserializers['{tns}FindRequestType'] = tns_FindRequestType_deserialize;
    this.globalElementSerializers['{tns}ReportIssueRequestType'] = tns_ReportIssueRequestType_serialize;
    this.globalElementDeserializers['{tns}ReportIssueRequestType'] = tns_ReportIssueRequestType_deserialize;
    this.globalElementSerializers['{tns}GetResultRequestType'] = tns_GetResultRequestType_serialize;
    this.globalElementDeserializers['{tns}GetResultRequestType'] = tns_GetResultRequestType_deserialize;
    this.globalElementSerializers['{tns}AppendResponseType'] = tns_AppendResponseType_serialize;
    this.globalElementDeserializers['{tns}AppendResponseType'] = tns_AppendResponseType_deserialize;
    this.globalElementSerializers['{tns}AppendRequestType'] = tns_AppendRequestType_serialize;
    this.globalElementDeserializers['{tns}AppendRequestType'] = tns_AppendRequestType_deserialize;
    this.globalElementSerializers['{tns}CreateRequestType'] = tns_CreateRequestType_serialize;
    this.globalElementDeserializers['{tns}CreateRequestType'] = tns_CreateRequestType_deserialize;
    this.globalElementSerializers['{tns}testexecutionresultArray'] = tns_testexecutionresultArray_serialize;
    this.globalElementDeserializers['{tns}testexecutionresultArray'] = tns_testexecutionresultArray_deserialize;
    this.globalElementSerializers['{tns}AppendResponseType'] = tns_AppendResponseType_serialize;
    this.globalElementDeserializers['{tns}AppendResponseType'] = tns_AppendResponseType_deserialize;
    this.globalElementSerializers['{tns}RunRequestType'] = tns_RunRequestType_serialize;
    this.globalElementDeserializers['{tns}RunRequestType'] = tns_RunRequestType_deserialize;
    this.globalElementSerializers['{tns}attachmentArray'] = tns_attachmentArray_serialize;
    this.globalElementDeserializers['{tns}attachmentArray'] = tns_attachmentArray_deserialize;
    this.globalElementSerializers['{tns}testscenarioArray'] = tns_testscenarioArray_serialize;
    this.globalElementDeserializers['{tns}testscenarioArray'] = tns_testscenarioArray_deserialize;
    this.globalElementSerializers['{tns}environment'] = tns_environment_serialize;
    this.globalElementDeserializers['{tns}environment'] = tns_environment_deserialize;
    this.globalElementSerializers['{tns}FindResponseType'] = tns_FindResponseType_serialize;
    this.globalElementDeserializers['{tns}FindResponseType'] = tns_FindResponseType_deserialize;
    this.globalElementSerializers['{tns}testexecutionresult'] = tns_testexecutionresult_serialize;
    this.globalElementDeserializers['{tns}testexecutionresult'] = tns_testexecutionresult_deserialize;
    this.globalElementSerializers['{tns}CreateResponseType'] = tns_CreateResponseType_serialize;
    this.globalElementDeserializers['{tns}CreateResponseType'] = tns_CreateResponseType_deserialize;
    this.globalElementSerializers['{tns}ReportIssueRequestType'] = tns_ReportIssueRequestType_serialize;
    this.globalElementDeserializers['{tns}ReportIssueRequestType'] = tns_ReportIssueRequestType_deserialize;
    this.globalElementSerializers['{tns}ReportIssueResponseType'] = tns_ReportIssueResponseType_serialize;
    this.globalElementDeserializers['{tns}ReportIssueResponseType'] = tns_ReportIssueResponseType_deserialize;
    this.globalElementSerializers['{tns}ReportFileResponseType'] = tns_ReportFileResponseType_serialize;
    this.globalElementDeserializers['{tns}ReportFileResponseType'] = tns_ReportFileResponseType_deserialize;
    this.globalElementSerializers['{tns}GetResultResponseType'] = tns_GetResultResponseType_serialize;
    this.globalElementDeserializers['{tns}GetResultResponseType'] = tns_GetResultResponseType_deserialize;
    this.globalElementSerializers['{tns}testscenario'] = tns_testscenario_serialize;
    this.globalElementDeserializers['{tns}testscenario'] = tns_testscenario_deserialize;
    this.globalElementSerializers['{tns}testexecution'] = tns_testexecution_serialize;
    this.globalElementDeserializers['{tns}testexecution'] = tns_testexecution_deserialize;
    this.globalElementSerializers['{tns}attachment'] = tns_attachment_serialize;
    this.globalElementDeserializers['{tns}attachment'] = tns_attachment_deserialize;
    this.globalElementSerializers['{tns}ReportResultResponseType'] = tns_ReportResultResponseType_serialize;
    this.globalElementDeserializers['{tns}ReportResultResponseType'] = tns_ReportResultResponseType_deserialize;
    this.globalElementSerializers['{tns}request'] = tns_request_serialize;
    this.globalElementDeserializers['{tns}request'] = tns_request_deserialize;
    this.globalElementSerializers['{tns}requestArray'] = tns_requestArray_serialize;
    this.globalElementDeserializers['{tns}requestArray'] = tns_requestArray_deserialize;
    this.globalElementSerializers['{tns}RunResponseType'] = tns_RunResponseType_serialize;
    this.globalElementDeserializers['{tns}RunResponseType'] = tns_RunResponseType_deserialize;
    this.globalElementSerializers['{tns}FindRequestType'] = tns_FindRequestType_serialize;
    this.globalElementDeserializers['{tns}FindRequestType'] = tns_FindRequestType_deserialize;
    this.globalElementSerializers['{tns}GetResultRequestType'] = tns_GetResultRequestType_serialize;
    this.globalElementDeserializers['{tns}GetResultRequestType'] = tns_GetResultRequestType_deserialize;
    this.globalElementSerializers['{tns}environmentArray'] = tns_environmentArray_serialize;
    this.globalElementDeserializers['{tns}environmentArray'] = tns_environmentArray_deserialize;
    this.globalElementSerializers['{tns}ReportFileRequestType'] = tns_ReportFileRequestType_serialize;
    this.globalElementDeserializers['{tns}ReportFileRequestType'] = tns_ReportFileRequestType_deserialize;
    this.globalElementSerializers['{tns}testexecutionArray'] = tns_testexecutionArray_serialize;
    this.globalElementDeserializers['{tns}testexecutionArray'] = tns_testexecutionArray_deserialize;
    this.globalElementSerializers['{tns}ReportResultRequestType'] = tns_ReportResultRequestType_serialize;
    this.globalElementDeserializers['{tns}ReportResultRequestType'] = tns_ReportResultRequestType_deserialize;
    this.globalElementSerializers['{tns}AppendRequestType'] = tns_AppendRequestType_serialize;
    this.globalElementDeserializers['{tns}AppendRequestType'] = tns_AppendRequestType_deserialize;
}

function tns_Run_op_onsuccess(client, responseXml) {
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
     this.jsutils.trace('calling tns_RunResponse_deserializeResponse');
     responseObject = tns_RunResponse_deserializeResponse(this.jsutils, element);
     client.user_onsuccess(responseObject);
    }
}

tns_TestServicePortType.prototype.Run_onsuccess = tns_Run_op_onsuccess;

function tns_Run_op_onerror(client) {
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

tns_TestServicePortType.prototype.Run_onerror = tns_Run_op_onerror;

//
// Operation {tns}Run
// Wrapped operation.
// parameter token
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter object
// - Object constructor is tns_testscenario
// parameter version
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter environment
// - Object constructor is tns_environment
//
function tns_Run_op(successCallback, errorCallback, token, object, version, environment) {
    this.client = new CxfApacheOrgClient(this.jsutils);
    var xml = null;
    var args = new Array(4);
    args[0] = token;
    args[1] = object;
    args[2] = version;
    args[3] = environment;
    xml = this.RunRequest_serializeInput(this.jsutils, args);
    this.client.user_onsuccess = successCallback;
    this.client.user_onerror = errorCallback;
    var closureThis = this;
    this.client.onsuccess = function(client, responseXml) { closureThis.Run_onsuccess(client, responseXml); };
    this.client.onerror = function(client) { closureThis.Run_onerror(client); };
    var requestHeaders = [];
    requestHeaders['SOAPAction'] = 'tns.Run';
    this.jsutils.trace('synchronous = ' + this.synchronous);
    this.client.request(this.url, xml, null, this.synchronous, requestHeaders);
}

tns_TestServicePortType.prototype.Run = tns_Run_op;

function tns_RunRequest_serializeInput(cxfjsutils, args) {
    var wrapperObj = new tns_RunRequestType();
    wrapperObj.setToken(args[0]);
    wrapperObj.setObject(args[1]);
    wrapperObj.setVersion(args[2]);
    wrapperObj.setEnvironment(args[3]);
    var xml;
    xml = cxfjsutils.beginSoap11Message("xmlns:jns0='tns' ");
    // block for local variables
    {
     xml = xml + wrapperObj.serialize(cxfjsutils, 'jns0:Run', null);
    }
    xml = xml + cxfjsutils.endSoap11Message();
    return xml;
}

tns_TestServicePortType.prototype.RunRequest_serializeInput = tns_RunRequest_serializeInput;

function tns_RunResponse_deserializeResponse(cxfjsutils, partElement) {
    var returnObject = tns_RunResponseType_deserialize (cxfjsutils, partElement);

    return returnObject;
}
function tns_Find_op_onsuccess(client, responseXml) {
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
     this.jsutils.trace('calling tns_FindResponse_deserializeResponse');
     responseObject = tns_FindResponse_deserializeResponse(this.jsutils, element);
     client.user_onsuccess(responseObject);
    }
}

tns_TestServicePortType.prototype.Find_onsuccess = tns_Find_op_onsuccess;

function tns_Find_op_onerror(client) {
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

tns_TestServicePortType.prototype.Find_onerror = tns_Find_op_onerror;

//
// Operation {tns}Find
// Wrapped operation.
// parameter token
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter object
// - Object constructor is tns_testscenario
//
function tns_Find_op(successCallback, errorCallback, token, object) {
    this.client = new CxfApacheOrgClient(this.jsutils);
    var xml = null;
    var args = new Array(2);
    args[0] = token;
    args[1] = object;
    xml = this.FindRequest_serializeInput(this.jsutils, args);
    this.client.user_onsuccess = successCallback;
    this.client.user_onerror = errorCallback;
    var closureThis = this;
    this.client.onsuccess = function(client, responseXml) { closureThis.Find_onsuccess(client, responseXml); };
    this.client.onerror = function(client) { closureThis.Find_onerror(client); };
    var requestHeaders = [];
    requestHeaders['SOAPAction'] = 'tns.Find';
    this.jsutils.trace('synchronous = ' + this.synchronous);
    this.client.request(this.url, xml, null, this.synchronous, requestHeaders);
}

tns_TestServicePortType.prototype.Find = tns_Find_op;

function tns_FindRequest_serializeInput(cxfjsutils, args) {
    var wrapperObj = new tns_FindRequestType();
    wrapperObj.setToken(args[0]);
    wrapperObj.setObject(args[1]);
    var xml;
    xml = cxfjsutils.beginSoap11Message("xmlns:jns0='tns' ");
    // block for local variables
    {
     xml = xml + wrapperObj.serialize(cxfjsutils, 'jns0:Find', null);
    }
    xml = xml + cxfjsutils.endSoap11Message();
    return xml;
}

tns_TestServicePortType.prototype.FindRequest_serializeInput = tns_FindRequest_serializeInput;

function tns_FindResponse_deserializeResponse(cxfjsutils, partElement) {
    var returnObject = tns_FindResponseType_deserialize (cxfjsutils, partElement);

    return returnObject;
}
function tns_ReportResult_op_onsuccess(client, responseXml) {
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
     this.jsutils.trace('calling tns_ReportResultResponse_deserializeResponse');
     responseObject = tns_ReportResultResponse_deserializeResponse(this.jsutils, element);
     client.user_onsuccess(responseObject);
    }
}

tns_TestServicePortType.prototype.ReportResult_onsuccess = tns_ReportResult_op_onsuccess;

function tns_ReportResult_op_onerror(client) {
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

tns_TestServicePortType.prototype.ReportResult_onerror = tns_ReportResult_op_onerror;

//
// Operation {tns}ReportResult
// Wrapped operation.
// parameter token
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter execution
// - Object constructor is tns_testexecution
// parameter test
// - Object constructor is tns_testscenario
// parameter result
// - Object constructor is tns_testexecutionresult
// parameter description
// - simple type {http://www.w3.org/2001/XMLSchema}string//
function tns_ReportResult_op(successCallback, errorCallback, token, execution, test, result, description) {
    this.client = new CxfApacheOrgClient(this.jsutils);
    var xml = null;
    var args = new Array(5);
    args[0] = token;
    args[1] = execution;
    args[2] = test;
    args[3] = result;
    args[4] = description;
    xml = this.ReportResultRequest_serializeInput(this.jsutils, args);
    this.client.user_onsuccess = successCallback;
    this.client.user_onerror = errorCallback;
    var closureThis = this;
    this.client.onsuccess = function(client, responseXml) { closureThis.ReportResult_onsuccess(client, responseXml); };
    this.client.onerror = function(client) { closureThis.ReportResult_onerror(client); };
    var requestHeaders = [];
    requestHeaders['SOAPAction'] = 'tns.ReportResult';
    this.jsutils.trace('synchronous = ' + this.synchronous);
    this.client.request(this.url, xml, null, this.synchronous, requestHeaders);
}

tns_TestServicePortType.prototype.ReportResult = tns_ReportResult_op;

function tns_ReportResultRequest_serializeInput(cxfjsutils, args) {
    var wrapperObj = new tns_ReportResultRequestType();
    wrapperObj.setToken(args[0]);
    wrapperObj.setExecution(args[1]);
    wrapperObj.setTest(args[2]);
    wrapperObj.setResult(args[3]);
    wrapperObj.setDescription(args[4]);
    var xml;
    xml = cxfjsutils.beginSoap11Message("xmlns:jns0='tns' ");
    // block for local variables
    {
     xml = xml + wrapperObj.serialize(cxfjsutils, 'jns0:ReportResult', null);
    }
    xml = xml + cxfjsutils.endSoap11Message();
    return xml;
}

tns_TestServicePortType.prototype.ReportResultRequest_serializeInput = tns_ReportResultRequest_serializeInput;

function tns_ReportResultResponse_deserializeResponse(cxfjsutils, partElement) {
}
function tns_ReportFile_op_onsuccess(client, responseXml) {
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
     this.jsutils.trace('calling tns_ReportFileResponse_deserializeResponse');
     responseObject = tns_ReportFileResponse_deserializeResponse(this.jsutils, element);
     client.user_onsuccess(responseObject);
    }
}

tns_TestServicePortType.prototype.ReportFile_onsuccess = tns_ReportFile_op_onsuccess;

function tns_ReportFile_op_onerror(client) {
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

tns_TestServicePortType.prototype.ReportFile_onerror = tns_ReportFile_op_onerror;

//
// Operation {tns}ReportFile
// Wrapped operation.
// parameter token
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter execution
// - Object constructor is tns_testexecution
// parameter test
// - Object constructor is tns_testscenario
// parameter file
// - Object constructor is tns_attachment
//
function tns_ReportFile_op(successCallback, errorCallback, token, execution, test, file) {
    this.client = new CxfApacheOrgClient(this.jsutils);
    var xml = null;
    var args = new Array(4);
    args[0] = token;
    args[1] = execution;
    args[2] = test;
    args[3] = file;
    xml = this.ReportFileRequest_serializeInput(this.jsutils, args);
    this.client.user_onsuccess = successCallback;
    this.client.user_onerror = errorCallback;
    var closureThis = this;
    this.client.onsuccess = function(client, responseXml) { closureThis.ReportFile_onsuccess(client, responseXml); };
    this.client.onerror = function(client) { closureThis.ReportFile_onerror(client); };
    var requestHeaders = [];
    requestHeaders['SOAPAction'] = 'tns.ReportFile';
    this.jsutils.trace('synchronous = ' + this.synchronous);
    this.client.request(this.url, xml, null, this.synchronous, requestHeaders);
}

tns_TestServicePortType.prototype.ReportFile = tns_ReportFile_op;

function tns_ReportFileRequest_serializeInput(cxfjsutils, args) {
    var wrapperObj = new tns_ReportFileRequestType();
    wrapperObj.setToken(args[0]);
    wrapperObj.setExecution(args[1]);
    wrapperObj.setTest(args[2]);
    wrapperObj.setFile(args[3]);
    var xml;
    xml = cxfjsutils.beginSoap11Message("xmlns:jns0='tns' ");
    // block for local variables
    {
     xml = xml + wrapperObj.serialize(cxfjsutils, 'jns0:ReportFile', null);
    }
    xml = xml + cxfjsutils.endSoap11Message();
    return xml;
}

tns_TestServicePortType.prototype.ReportFileRequest_serializeInput = tns_ReportFileRequest_serializeInput;

function tns_ReportFileResponse_deserializeResponse(cxfjsutils, partElement) {
}
function tns_Create_op_onsuccess(client, responseXml) {
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
     this.jsutils.trace('calling tns_CreateResponse_deserializeResponse');
     responseObject = tns_CreateResponse_deserializeResponse(this.jsutils, element);
     client.user_onsuccess(responseObject);
    }
}

tns_TestServicePortType.prototype.Create_onsuccess = tns_Create_op_onsuccess;

function tns_Create_op_onerror(client) {
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

tns_TestServicePortType.prototype.Create_onerror = tns_Create_op_onerror;

//
// Operation {tns}Create
// Wrapped operation.
// parameter token
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter object
// - Object constructor is tns_testscenario
//
function tns_Create_op(successCallback, errorCallback, token, object) {
    this.client = new CxfApacheOrgClient(this.jsutils);
    var xml = null;
    var args = new Array(2);
    args[0] = token;
    args[1] = object;
    xml = this.CreateRequest_serializeInput(this.jsutils, args);
    this.client.user_onsuccess = successCallback;
    this.client.user_onerror = errorCallback;
    var closureThis = this;
    this.client.onsuccess = function(client, responseXml) { closureThis.Create_onsuccess(client, responseXml); };
    this.client.onerror = function(client) { closureThis.Create_onerror(client); };
    var requestHeaders = [];
    requestHeaders['SOAPAction'] = 'tns.Create';
    this.jsutils.trace('synchronous = ' + this.synchronous);
    this.client.request(this.url, xml, null, this.synchronous, requestHeaders);
}

tns_TestServicePortType.prototype.Create = tns_Create_op;

function tns_CreateRequest_serializeInput(cxfjsutils, args) {
    var wrapperObj = new tns_CreateRequestType();
    wrapperObj.setToken(args[0]);
    wrapperObj.setObject(args[1]);
    var xml;
    xml = cxfjsutils.beginSoap11Message("xmlns:jns0='tns' ");
    // block for local variables
    {
     xml = xml + wrapperObj.serialize(cxfjsutils, 'jns0:Create', null);
    }
    xml = xml + cxfjsutils.endSoap11Message();
    return xml;
}

tns_TestServicePortType.prototype.CreateRequest_serializeInput = tns_CreateRequest_serializeInput;

function tns_CreateResponse_deserializeResponse(cxfjsutils, partElement) {
    var returnObject = tns_CreateResponseType_deserialize (cxfjsutils, partElement);

    return returnObject;
}
function tns_ReportIssue_op_onsuccess(client, responseXml) {
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
     this.jsutils.trace('calling tns_ReportIssueResponse_deserializeResponse');
     responseObject = tns_ReportIssueResponse_deserializeResponse(this.jsutils, element);
     client.user_onsuccess(responseObject);
    }
}

tns_TestServicePortType.prototype.ReportIssue_onsuccess = tns_ReportIssue_op_onsuccess;

function tns_ReportIssue_op_onerror(client) {
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

tns_TestServicePortType.prototype.ReportIssue_onerror = tns_ReportIssue_op_onerror;

//
// Operation {tns}ReportIssue
// Wrapped operation.
// parameter token
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter execution
// - Object constructor is tns_testexecution
// parameter test
// - Object constructor is tns_testscenario
// parameter issue
// - Object constructor is tns_request
//
function tns_ReportIssue_op(successCallback, errorCallback, token, execution, test, issue) {
    this.client = new CxfApacheOrgClient(this.jsutils);
    var xml = null;
    var args = new Array(4);
    args[0] = token;
    args[1] = execution;
    args[2] = test;
    args[3] = issue;
    xml = this.ReportIssueRequest_serializeInput(this.jsutils, args);
    this.client.user_onsuccess = successCallback;
    this.client.user_onerror = errorCallback;
    var closureThis = this;
    this.client.onsuccess = function(client, responseXml) { closureThis.ReportIssue_onsuccess(client, responseXml); };
    this.client.onerror = function(client) { closureThis.ReportIssue_onerror(client); };
    var requestHeaders = [];
    requestHeaders['SOAPAction'] = 'tns.ReportIssue';
    this.jsutils.trace('synchronous = ' + this.synchronous);
    this.client.request(this.url, xml, null, this.synchronous, requestHeaders);
}

tns_TestServicePortType.prototype.ReportIssue = tns_ReportIssue_op;

function tns_ReportIssueRequest_serializeInput(cxfjsutils, args) {
    var wrapperObj = new tns_ReportIssueRequestType();
    wrapperObj.setToken(args[0]);
    wrapperObj.setExecution(args[1]);
    wrapperObj.setTest(args[2]);
    wrapperObj.setIssue(args[3]);
    var xml;
    xml = cxfjsutils.beginSoap11Message("xmlns:jns0='tns' ");
    // block for local variables
    {
     xml = xml + wrapperObj.serialize(cxfjsutils, 'jns0:ReportIssue', null);
    }
    xml = xml + cxfjsutils.endSoap11Message();
    return xml;
}

tns_TestServicePortType.prototype.ReportIssueRequest_serializeInput = tns_ReportIssueRequest_serializeInput;

function tns_ReportIssueResponse_deserializeResponse(cxfjsutils, partElement) {
    var returnObject = tns_ReportIssueResponseType_deserialize (cxfjsutils, partElement);

    return returnObject;
}
function tns_GetResult_op_onsuccess(client, responseXml) {
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
     this.jsutils.trace('calling tns_GetResultResponse_deserializeResponse');
     responseObject = tns_GetResultResponse_deserializeResponse(this.jsutils, element);
     client.user_onsuccess(responseObject);
    }
}

tns_TestServicePortType.prototype.GetResult_onsuccess = tns_GetResult_op_onsuccess;

function tns_GetResult_op_onerror(client) {
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

tns_TestServicePortType.prototype.GetResult_onerror = tns_GetResult_op_onerror;

//
// Operation {tns}GetResult
// Wrapped operation.
// parameter token
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter execution
// - Object constructor is tns_testexecution
//
function tns_GetResult_op(successCallback, errorCallback, token, execution) {
    this.client = new CxfApacheOrgClient(this.jsutils);
    var xml = null;
    var args = new Array(2);
    args[0] = token;
    args[1] = execution;
    xml = this.GetResultRequest_serializeInput(this.jsutils, args);
    this.client.user_onsuccess = successCallback;
    this.client.user_onerror = errorCallback;
    var closureThis = this;
    this.client.onsuccess = function(client, responseXml) { closureThis.GetResult_onsuccess(client, responseXml); };
    this.client.onerror = function(client) { closureThis.GetResult_onerror(client); };
    var requestHeaders = [];
    requestHeaders['SOAPAction'] = 'tns.GetResult';
    this.jsutils.trace('synchronous = ' + this.synchronous);
    this.client.request(this.url, xml, null, this.synchronous, requestHeaders);
}

tns_TestServicePortType.prototype.GetResult = tns_GetResult_op;

function tns_GetResultRequest_serializeInput(cxfjsutils, args) {
    var wrapperObj = new tns_GetResultRequestType();
    wrapperObj.setToken(args[0]);
    wrapperObj.setExecution(args[1]);
    var xml;
    xml = cxfjsutils.beginSoap11Message("xmlns:jns0='tns' ");
    // block for local variables
    {
     xml = xml + wrapperObj.serialize(cxfjsutils, 'jns0:GetResult', null);
    }
    xml = xml + cxfjsutils.endSoap11Message();
    return xml;
}

tns_TestServicePortType.prototype.GetResultRequest_serializeInput = tns_GetResultRequest_serializeInput;

function tns_GetResultResponse_deserializeResponse(cxfjsutils, partElement) {
    var returnObject = tns_GetResultResponseType_deserialize (cxfjsutils, partElement);

    return returnObject;
}
function tns_Append_op_onsuccess(client, responseXml) {
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
     this.jsutils.trace('calling tns_AppendResponse_deserializeResponse');
     responseObject = tns_AppendResponse_deserializeResponse(this.jsutils, element);
     client.user_onsuccess(responseObject);
    }
}

tns_TestServicePortType.prototype.Append_onsuccess = tns_Append_op_onsuccess;

function tns_Append_op_onerror(client) {
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

tns_TestServicePortType.prototype.Append_onerror = tns_Append_op_onerror;

//
// Operation {tns}Append
// Wrapped operation.
// parameter token
// - simple type {http://www.w3.org/2001/XMLSchema}string// parameter parent
// - Object constructor is tns_testscenario
// parameter object
// - Object constructor is tns_testscenario
//
function tns_Append_op(successCallback, errorCallback, token, parent, object) {
    this.client = new CxfApacheOrgClient(this.jsutils);
    var xml = null;
    var args = new Array(3);
    args[0] = token;
    args[1] = parent;
    args[2] = object;
    xml = this.AppendRequest_serializeInput(this.jsutils, args);
    this.client.user_onsuccess = successCallback;
    this.client.user_onerror = errorCallback;
    var closureThis = this;
    this.client.onsuccess = function(client, responseXml) { closureThis.Append_onsuccess(client, responseXml); };
    this.client.onerror = function(client) { closureThis.Append_onerror(client); };
    var requestHeaders = [];
    requestHeaders['SOAPAction'] = 'tns.Append';
    this.jsutils.trace('synchronous = ' + this.synchronous);
    this.client.request(this.url, xml, null, this.synchronous, requestHeaders);
}

tns_TestServicePortType.prototype.Append = tns_Append_op;

function tns_AppendRequest_serializeInput(cxfjsutils, args) {
    var wrapperObj = new tns_AppendRequestType();
    wrapperObj.setToken(args[0]);
    wrapperObj.setParent(args[1]);
    wrapperObj.setObject(args[2]);
    var xml;
    xml = cxfjsutils.beginSoap11Message("xmlns:jns0='tns' ");
    // block for local variables
    {
     xml = xml + wrapperObj.serialize(cxfjsutils, 'jns0:Append', null);
    }
    xml = xml + cxfjsutils.endSoap11Message();
    return xml;
}

tns_TestServicePortType.prototype.AppendRequest_serializeInput = tns_AppendRequest_serializeInput;

function tns_AppendResponse_deserializeResponse(cxfjsutils, partElement) {
    var returnObject = tns_AppendResponseType_deserialize (cxfjsutils, partElement);

    return returnObject;
}
function tns_TestServicePortType_tns_TestServicePort () {
  this.url = 'http://Saturn/api/testservice';
}
tns_TestServicePortType_tns_TestServicePort.prototype = new tns_TestServicePortType;
