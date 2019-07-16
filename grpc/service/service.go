package main

import (
    "flag"
    "fmt"
    "net"
    "dev/course/service" // 实现了服务接口的包service
    "dev/course/protos"  // 此为自定义的protos包，存放的是.proto文件和对应的.pb.go文件
    "google.golang.org/grpc"
    "dev/course/model/db"

    // "dev/course/model"
    // "os"
    // "reflect"
)

var (
	addr = flag.String("host", "127.0.0.1:8888", "");
)

func main() {
    islink := db.GetInstance().InitDataPool()
    if islink == false {
        fmt.Println("db error!")
    }

    // p := make(map[string]string)
	// v, err := model.SelectCourseList(p)
    // fmt.Println(v, reflect.TypeOf(v))
    // os.Exit(1)

	lis, err := net.Listen("tcp", *addr)
	if err != nil {
		fmt.Println(err)
		return
	}

	// 创建一个grpc服务
    grpcServer := grpc.NewServer()
    // 重点：向grpc服务中注册一个api服务，这里是UserService，处理相关请求
    course.RegisterCourseServiceServer(grpcServer, service.NewCourseService())
    // 可以添加多个api
    // TODO...

    // 启动grpc服务
    grpcServer.Serve(lis)
}