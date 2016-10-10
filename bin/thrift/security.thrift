namespace cpp proxy.security
namespace php proxy.security
namespace py  proxy.security

struct ResultStruct {
  1: i32    err,
  2: string msg,
  3: string str
}

struct ResultListWithFlag {
  1: string flag,
  2: map<string, ResultStruct> BatchResultStruct
}

service SecurityService {
  ResultStruct encrypt(1: string str),
  ResultStruct decrypt(1: string str),
  map<string, ResultStruct> batchEncrypt(1: map<string, string> vec),
  map<string, ResultStruct> batchDecrypt(1: map<string, string> vec),
  ResultListWithFlag batchEncryptWithFlag(1: string flag, 2: map<string, string> vec),
  ResultListWithFlag batchDecryptWithFlag(1: string flag, 2: map<string, string> vec)
}
