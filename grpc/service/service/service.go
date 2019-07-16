package service

import (
	protos "dev/course/protos"
	"golang.org/x/net/context"
	"dev/course/router"
	"reflect"
	"errors"
	"log"
	"encoding/json"
	"fmt"
	// "io"
	// "os"
)

type CourseService struct {

}

func NewCourseService() *CourseService {
	return &CourseService{}
}

// StreamGet接口方法实现
// func (this *CourseService) StreamGet(reqStream protos.CourseService_StreamGetServer) error {
// 	// 接收流式请求并返回单一对象
// 	values := make(map[string]string)
//     for {
//         userReq, err := reqStream.Recv()
//         if err != io.EOF {
//             fmt.Println("流请求~", *userReq)
//         } else {
//             return reqStream.SendAndClose(&protos.CourseResponse{
// 				Status: 200,
// 				Values: values,
// 			})
//         }
//     }
// }

// todo 错误处理和返回后面要完善！
func (this *CourseService) Get(ctx context.Context, req *protos.CourseRequest) (*protos.CourseResponse, error) {
	result, err := this.Call(req.GetRouter(), req.GetParameters())
	if err != nil {
		// fmt.Println(err)
		log.Fatal(err) // todo 是否直接返回好一些？？？
	}

	DataJsonBytes, err := json.Marshal(result[0].Interface())
	if err != nil {
		fmt.Println(err)
		log.Fatal(err)
	}

	values := make(map[string]string)
	values["data"] = string(DataJsonBytes)
	if len(result) > 2 {
        OtherJsonBytes, err := json.Marshal(result[2].Interface())
		if err != nil {
			log.Fatal(err)
		}
		values["other"] = string(OtherJsonBytes)
	}
	
	return &protos.CourseResponse{
		Status: 200,
		Values: values,
	}, nil
}

func (this *CourseService) Call(name string, params ... interface{}) (result []reflect.Value, err error) {
	if _, ok := router.Routers[name]; !ok {
        err = errors.New(name + " does not exist.")
        return
	}
	
    f := reflect.ValueOf(router.Routers[name])
    if len(params) != f.Type().NumIn() {
        err = errors.New("The number of params is not adapted.")
        return
	}

    in := make([]reflect.Value, len(params))
    for k, param := range params {
        in[k] = reflect.ValueOf(param)
	}
	
	result = f.Call(in)
	if result[1].Interface() != nil {
		err = result[1].Interface().(error)
	}
	return
}